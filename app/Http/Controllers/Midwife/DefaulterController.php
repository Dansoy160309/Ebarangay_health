<?php

namespace App\Http\Controllers\Midwife;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\MessageDispatchLog;
use App\Mail\DefaulterRecallMail;
use App\Services\ReminderPolicyService;
use App\Services\TemplateService;
use App\Notifications\DefaulterRecallNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class DefaulterController extends Controller
{
    /**
     * Display a list of "Defaulters" (patients who missed scheduled doses)
     * Queries appointments with 'no_show' status (auto-marked when appointment time passes)
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['user.guardian', 'slot'])
            ->where('status', 'no_show');

        // Search Logic
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($sq) use ($search) {
                $sq->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        // Filter by service if provided
        $serviceFilter = $request->get('service', 'all');
        if ($serviceFilter !== 'all') {
            $query->where('service', 'like', "%{$serviceFilter}%");
        } else {
            // Default: Only critical services (Immunization & Prenatal)
            $query->where(function($q) {
                $q->where('service', 'like', '%Immunization%')
                  ->orWhere('service', 'like', '%Prenatal%');
            });
        }

        $allDefaulters = (clone $query)->get();
        $stats = [
            'total' => $allDefaulters->count(),
            'immunization' => $allDefaulters->filter(fn($a) => str_contains(strtolower($a->service), 'immunization'))->count(),
            'prenatal' => $allDefaulters->filter(fn($a) => str_contains(strtolower($a->service), 'prenatal'))->count(),
        ];

        $defaulters = $query->orderBy('scheduled_at', 'desc')->paginate(15)->appends($request->query());

        return view('midwife.appointments.defaulters', compact('defaulters', 'stats', 'serviceFilter'));
    }

    /**
     * Mark an appointment as "No Show"
     */
    public function markAsNoShow(Appointment $appointment)
    {
        $appointment->update(['status' => 'no_show']);
        return redirect()->back()->with('success', 'Appointment marked as No Show.');
    }

    /**
     * Send a templated email to patient/guardian using fresh data
     */
    public function sendEmailTemplate(Appointment $appointment, ReminderPolicyService $policy)
    {
        try {
            $appointment->load(['user.guardian', 'slot']);

            if ($appointment->status !== 'no_show') {
                return redirect()->back()->with('error', 'This appointment is already resolved. No further reminders are needed.');
            }

            $patient = $appointment->user;
            $stage = $policy->nextManualStage($appointment->id);

            if ($stage > 3) {
                return redirect()->back()->with('error', 'Maximum reminder attempts reached for this case.');
            }
            
            // Get contact email (fresh data)
            $isDependent = $patient->isDependent();
            $contactEmail = $isDependent
                ? optional($patient->guardian)->email
                : $patient->email;

            if (empty($contactEmail)) {
                MessageDispatchLog::create([
                    'appointment_id' => $appointment->id,
                    'patient_user_id' => $patient?->id,
                    'sender_user_id' => auth()->id(),
                    'template_id' => null,
                    'category' => ReminderPolicyService::CATEGORY,
                    'channel' => 'email',
                    'stage' => $stage,
                    'trigger_mode' => 'manual',
                    'status' => 'skipped',
                    'recipient' => null,
                    'reason' => 'missing_email',
                    'sent_at' => now(),
                ]);

                return redirect()->back()->with('error', 'Patient or guardian has no email address registered.');
            }

            // Render template with fresh appointment data
            $rendered = TemplateService::render('defaulter_recall_email', $appointment);

            // Send email via Hostinger SMTP
            Mail::to($contactEmail)->send(new DefaulterRecallMail(
                $rendered['subject'],
                $rendered['body'],
                $contactEmail
            ));

            MessageDispatchLog::create([
                'appointment_id' => $appointment->id,
                'patient_user_id' => $patient->id,
                'sender_user_id' => auth()->id(),
                'template_id' => $rendered['template_id'] ?? null,
                'category' => ReminderPolicyService::CATEGORY,
                'channel' => 'email',
                'stage' => $stage,
                'trigger_mode' => 'manual',
                'status' => 'sent',
                'recipient' => $contactEmail,
                'sent_at' => now(),
            ]);

            \Log::info('Defaulter recall email sent', [
                'appointment_id' => $appointment->id,
                'patient_id' => $patient->id,
                'recipient' => $contactEmail,
                'template_id' => $rendered['template_id'],
            ]);

            return redirect()->back()->with('success', "Email successfully sent to {$contactEmail}.");
        } catch (\Exception $e) {
            MessageDispatchLog::create([
                'appointment_id' => $appointment->id,
                'patient_user_id' => $appointment->user_id,
                'sender_user_id' => auth()->id(),
                'template_id' => null,
                'category' => ReminderPolicyService::CATEGORY,
                'channel' => 'email',
                'stage' => $policy->nextManualStage($appointment->id),
                'trigger_mode' => 'manual',
                'status' => 'failed',
                'recipient' => null,
                'reason' => 'send_exception',
                'provider_response' => $e->getMessage(),
                'sent_at' => now(),
            ]);

            \Log::error('Failed to send defaulter recall email', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to send email. Please try again.');
        }
    }

    /**
     * Send a templated SMS to patient/guardian using fresh data
     */
    public function sendSmsTemplate(Appointment $appointment, ReminderPolicyService $policy)
    {
        try {
            $appointment->load(['user.guardian', 'slot']);

            if ($appointment->status !== 'no_show') {
                return redirect()->back()->with('error', 'This appointment is already resolved. No further reminders are needed.');
            }

            $patient = $appointment->user;
            $stage = $policy->nextManualStage($appointment->id);

            if ($stage > 3) {
                return redirect()->back()->with('error', 'Maximum reminder attempts reached for this case.');
            }

            // Get contact number (fresh data)
            $recipient = $patient->contact_no;
            if (empty($recipient) && $patient->isDependent()) {
                $recipient = optional($patient->guardian)->contact_no;
            }

            if (empty($recipient)) {
                MessageDispatchLog::create([
                    'appointment_id' => $appointment->id,
                    'patient_user_id' => $patient?->id,
                    'sender_user_id' => auth()->id(),
                    'template_id' => null,
                    'category' => ReminderPolicyService::CATEGORY,
                    'channel' => 'sms',
                    'stage' => $stage,
                    'trigger_mode' => 'manual',
                    'status' => 'skipped',
                    'recipient' => null,
                    'reason' => 'missing_contact_number',
                    'sent_at' => now(),
                ]);

                return redirect()->back()->with('error', 'Patient or guardian has no contact number registered.');
            }

            // Render SMS template with fresh data
            $rendered = TemplateService::render('defaulter_recall_sms', $appointment);

            // Send via notification (which uses PhilSMS channel)
            $patient->notify(new DefaulterRecallNotification($appointment, $rendered['body']));

            MessageDispatchLog::create([
                'appointment_id' => $appointment->id,
                'patient_user_id' => $patient->id,
                'sender_user_id' => auth()->id(),
                'template_id' => $rendered['template_id'] ?? null,
                'category' => ReminderPolicyService::CATEGORY,
                'channel' => 'sms',
                'stage' => $stage,
                'trigger_mode' => 'manual',
                'status' => 'sent',
                'recipient' => $recipient,
                'sent_at' => now(),
            ]);

            \Log::info('Defaulter recall SMS sent', [
                'appointment_id' => $appointment->id,
                'patient_id' => $patient->id,
                'recipient' => $recipient,
                'template_id' => $rendered['template_id'],
            ]);

            return redirect()->back()->with('success', "SMS successfully sent to {$recipient}.");
        } catch (\Exception $e) {
            MessageDispatchLog::create([
                'appointment_id' => $appointment->id,
                'patient_user_id' => $appointment->user_id,
                'sender_user_id' => auth()->id(),
                'template_id' => null,
                'category' => ReminderPolicyService::CATEGORY,
                'channel' => 'sms',
                'stage' => $policy->nextManualStage($appointment->id),
                'trigger_mode' => 'manual',
                'status' => 'failed',
                'recipient' => null,
                'reason' => 'send_exception',
                'provider_response' => $e->getMessage(),
                'sent_at' => now(),
            ]);

            \Log::error('Failed to send defaulter recall SMS', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to send SMS. Please try again.');
        }
    }

    /**
     * Open the patient's email client with a prefilled recall message (deprecated, kept for reference)
     */
    public function sendRecallEmail(Appointment $appointment)
    {
        $appointment->load(['user.guardian', 'slot']);

        $patient = $appointment->user;
        $isDependent = $patient->isDependent();
        $recipient = $isDependent
            ? optional($patient->guardian)->email
            : $patient->email;

        if (empty($recipient)) {
            return redirect()->back()->with('error', 'Patient or guardian has no email address registered.');
        }

        $patientName = $patient->full_name;
        $serviceName = $appointment->service;
        $scheduledDate = $appointment->scheduled_at
            ? $appointment->scheduled_at->format('M d, Y')
            : ($appointment->slot ? \Carbon\Carbon::parse($appointment->slot->date)->format('M d, Y') : 'N/A');
        $scheduledTime = $appointment->scheduled_at ? $appointment->scheduled_at->format('h:i A') : 'N/A';
        $recipientLabel = $isDependent
            ? (optional($patient->guardian)->first_name ?? 'Parent/Guardian')
            : $patientName;

        $subject = rawurlencode("E-Barangay Follow-Up: Missed Appointment - {$patientName}");
        $body = rawurlencode(
"Dear {$recipientLabel},

This is a reminder from E-Barangay Health Center regarding a missed {$serviceName} appointment for {$patientName} on {$scheduledDate} at {$scheduledTime}.

Please reply to this email or contact the barangay health center to assist with rescheduling as soon as possible.

Thank you for your cooperation.

Sincerely,
E-Barangay Health Team"
        );

        return redirect()->away("mailto:{$recipient}?subject={$subject}&body={$body}");
    }
}
