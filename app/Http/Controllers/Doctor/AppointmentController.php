<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\MessageDispatchLog;
use App\Models\MessageTemplate;
use App\Models\HealthRecord;
use App\Models\Service;
use App\Mail\DefaulterRecallMail;
use App\Notifications\UpcomingAppointmentReminder;
use App\Services\TemplateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Inline safeguard: normalize past approved/rescheduled to no_show
        // This complements the scheduled command and ensures UI correctness even if scheduler isn't running.
        $today = now()->startOfDay();
        try {
            Appointment::whereIn('status', ['approved', 'rescheduled'])
                ->where(function ($q) use ($today) {
                    $q->whereHas('slot', function ($sq) use ($today) {
                        $sq->whereDate('date', '<', $today->toDateString());
                    })->orWhere(function ($qq) use ($today) {
                        $qq->whereNull('slot_id')
                           ->whereDate('scheduled_at', '<', $today);
                    });
                })
                ->update(['status' => 'no_show']);
        } catch (\Throwable $e) {
            // Ignore if DB enum not yet migrated; UI fallback will still show correct badge.
        }

        $appointments = Appointment::with(['user', 'slot', 'healthRecord'])
            // Only show appointments assigned to this provider
            ->whereHas('slot', function($q) use ($user) {
                $q->where(function($sq) use ($user) {
                    $sq->where('doctor_id', $user->id);
                    // If midwife, also show slots with no doctor assigned (midwife-led)
                    if ($user->isMidwife()) {
                        $sq->orWhereNull('doctor_id');
                    }
                });
            });

        // Status filter
        // default: show active items (approved + rescheduled)
        $status = $request->get('status');
        if ($status) {
            $statusMap = [
                'active'    => ['approved', 'rescheduled'],
                'completed' => ['completed'],
                'cancelled' => ['cancelled', 'rejected'],
                'no_show'   => ['no_show'],
                'archived'  => ['archived'],
                'pending'   => ['pending'],
                'all'       => ['pending','approved','rescheduled','completed','cancelled','rejected'],
            ];
            $statuses = $statusMap[$status] ?? ['approved', 'rescheduled'];
            // Special handling for "no_show": include past approved/rescheduled even if DB not updated
            if ($status === 'no_show') {
                $appointments->where(function ($q) use ($today) {
                    $q->where('status', 'no_show')
                      ->orWhere(function ($qq) use ($today) {
                          $qq->whereIn('status', ['approved', 'rescheduled'])
                             ->where(function ($aq) use ($today) {
                                 $aq->whereHas('slot', function ($sq) use ($today) {
                                     $sq->whereDate('date', '<', $today->toDateString());
                                 })->orWhere(function ($sq2) use ($today) {
                                     $sq2->whereNull('slot_id')
                                         ->whereDate('scheduled_at', '<', $today);
                                 });
                             });
                      });
                });
            } else {
                $appointments->whereIn('status', $statuses);
            }
        } else {
            $appointments->whereIn('status', ['approved', 'rescheduled']);
        }

        // Hide past appointments from "active" view by default
        // Show only today and future schedules
        if (!$status || $status === 'active') {
            $today = now()->startOfDay();
            $appointments->where(function ($q) use ($today) {
                $q->whereHas('slot', function ($sq) use ($today) {
                    $sq->whereDate('date', '>=', $today->toDateString());
                })->orWhere(function ($qq) use ($today) {
                    $qq->whereNull('slot_id')
                       ->whereDate('scheduled_at', '>=', $today);
                });
            });
        }

        // Date filter (scheduled_at date)
        if ($request->filled('date')) {
            try {
                $date = \Carbon\Carbon::parse($request->date)->toDateString();
                $appointments->where(function ($q) use ($date) {
                    $q->whereHas('slot', function ($sq) use ($date) {
                        $sq->whereDate('date', $date);
                    })->orWhereDate('scheduled_at', $date);
                });
            } catch (\Throwable $e) {
                // ignore invalid date
            }
        }

        // Search by patient
        if ($request->filled('search')) {
            $s = $request->search;
            $appointments->whereHas('user', function($uq) use ($s) {
                $uq->where('first_name', 'like', "%{$s}%")
                   ->orWhere('last_name', 'like', "%{$s}%")
                   ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$s}%"]);
            });
        }

        // Calculate Stats for Header
        $stats = [
            'today_total' => (clone $appointments)->where(function($q) use ($today) {
                $q->whereHas('slot', function($sq) use ($today) {
                    $sq->whereDate('date', $today->toDateString());
                })->orWhereDate('scheduled_at', $today);
            })->count(),
            'ready' => (clone $appointments)->where('status', 'approved')
                ->whereHas('healthRecord', function($q) {
                    $q->whereNotNull('vital_signs');
                })->count(),
            'pending_vitals' => (clone $appointments)->where('status', 'approved')
                ->whereDoesntHave('healthRecord')->count(),
        ];

        // Get list of appointment IDs that need completion
        $readyAppointmentIds = (clone $appointments)->where('status', 'approved')
            ->whereHas('healthRecord', function($q) {
                $q->whereNotNull('vital_signs');
            })->pluck('id')->toArray();

        $appointments = $appointments
            ->latest('scheduled_at')
            ->paginate(15)
            ->appends($request->query());

        return view('doctor.appointments.index', compact('appointments', 'stats', 'readyAppointmentIds'));
    }

    public function show(Appointment $appointment)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        // Load patient history and current health record
        $appointment->load(['user.healthRecords' => function($q) {
            $q->with('service')->latest()->limit(5);
        }, 'healthRecord', 'user.patientProfile']);

        // Load available vaccine batches if this is an immunization appointment
        $vaccineBatches = [];
        if (str_contains(strtolower($appointment->service), 'immunization')) {
            $vaccineBatches = \App\Models\VaccineBatch::with('vaccine')
                ->where('expiry_date', '>', now())
                ->where('quantity_remaining', '>', 0)
                ->where('is_active', true)
                ->get()
                ->groupBy('vaccine.name');
        }
        
        return view('doctor.appointments.show', compact('appointment', 'vaccineBatches'));
    }

    public function consult(Request $request, Appointment $appointment)
    {
        $healthRecord = $appointment->healthRecord;

        if ($healthRecord && !empty($healthRecord->metadata['signature']['signed_at'] ?? null)) {
            return redirect()->back()->with('error', 'This record is already signed and locked. Create an amendment for corrections.');
        }

        $request->validate([
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'notes' => 'nullable|string',
            'metadata' => 'nullable|array',
            'profile' => 'nullable|array',
        ]);

        // 🛡️ Clinical Safety Rules for Immunization
        $isImmunization = str_contains(strtolower($appointment->service), 'immunization');
        if ($isImmunization) {
            // 1. Fever Check
            $vitals = $healthRecord->vital_signs ?? [];
            $temp = (float) ($vitals['temperature'] ?? 0);
            if ($temp > 37.5) {
                return redirect()->back()->withErrors(['error' => 'Safety Block: Vaccination cannot proceed due to patient fever (' . $temp . '°C).'])->withInput();
            }

            // 2. Stock Check
            if ($request->has('metadata.vaccine_batch_id')) {
                $batch = \App\Models\VaccineBatch::find($request->input('metadata.vaccine_batch_id'));
                if (!$batch || $batch->quantity_remaining <= 0) {
                    return redirect()->back()->withErrors(['error' => 'Safety Block: The selected vaccine batch is out of stock.'])->withInput();
                }
                if ($batch->expiry_date->isPast()) {
                    return redirect()->back()->withErrors(['error' => 'Safety Block: The selected vaccine batch has expired.'])->withInput();
                }
            }
        }

        DB::transaction(function () use ($request, $appointment, $isImmunization) {
            // 1. Update or Create Patient Profile if provided (for Prenatal)
            if ($request->has('profile')) {
                $profile = $appointment->user->patientProfile ?? new \App\Models\PatientProfile(['user_id' => $appointment->user_id]);
                
                $profileData = $request->input('profile');
                
                // Handle checkboxes
                $profileData['history_miscarriage'] = $request->has('profile.history_miscarriage');
                $profileData['is_high_risk'] = $request->has('profile.is_high_risk');

                $profile->fill($profileData);
                $profile->save();
                
                // Re-evaluate risk factors (auto-calculates based on age, gravida, etc.)
                $profile->evaluateRisk();
            }

            // 2. Handle Health Record
            $healthRecord = $appointment->healthRecord ?? new HealthRecord();
            
            // Try to find service ID if not set
            $serviceId = $healthRecord->service_id;
            if (!$serviceId && $appointment->service) {
                $service = Service::where('name', $appointment->service)->first();
                $serviceId = $service?->id;
            }

            // 💉 Immunization Specific: Suggest Next Dose
            $metadata = array_merge(
                $healthRecord->metadata ?? [], 
                $request->input('metadata', []),
                ['doctor_notes' => $request->notes]
            );

            // Clear old signature if record is re-saved before signing.
            unset($metadata['signature']);

            if ($isImmunization) {
                $doseNo = (int) ($request->input('metadata.dose_no') ?? 0);
                $nextSchedule = null;
                
                if ($doseNo >= 1 && $doseNo < 3) {
                    $nextSchedule = now()->addWeeks(4)->format('Y-m-d');
                    $metadata['suggested_next_dose_date'] = $nextSchedule;
                    $metadata['schedule_notes'] = 'Suggested 4-week interval per DOH guidelines.';
                }
            }

            $healthRecord->fill([
                'patient_id' => $appointment->user_id,
                'appointment_id' => $appointment->id,
                'service_id' => $serviceId,
                'diagnosis' => $request->diagnosis,
                'treatment' => $request->treatment,
                'consultation' => $request->notes, // Map notes to the consultation field
                'metadata' => $metadata,
                'created_by' => $healthRecord->created_by ?? auth()->id(),
                'verified_by' => $healthRecord->verified_by,
                'verified_at' => $healthRecord->verified_at,
            ]);

            if (!$healthRecord->exists && !$healthRecord->vital_signs) {
                $healthRecord->vital_signs = [];
            }

            $healthRecord->save();

            // 3. Decrement Vaccine Stock
            if ($isImmunization && $request->has('metadata.vaccine_batch_id')) {
                $batchId = $request->input('metadata.vaccine_batch_id');
                $qty = (int) ($request->input('metadata.dose_quantity') ?? 1);
                $qty = $qty > 0 ? $qty : 1;

                $batch = \App\Models\VaccineBatch::whereKey($batchId)->lockForUpdate()->first();
                if ($batch && !$batch->expiry_date->isPast() && $batch->is_active) {
                    $alreadyLogged = \App\Models\VaccineAdministration::where('appointment_id', $appointment->id)
                        ->where('vaccine_batch_id', $batch->id)
                        ->exists();

                    if (!$alreadyLogged && $batch->quantity_remaining >= $qty) {
                        $batch->quantity_remaining -= $qty;
                        $batch->quantity_administered += $qty;
                        if ($batch->quantity_remaining <= 0) {
                            $batch->is_active = false;
                        }
                        $batch->save();

                        \App\Models\VaccineAdministration::create([
                            'vaccine_id' => $batch->vaccine_id,
                            'vaccine_batch_id' => $batch->id,
                            'patient_id' => $appointment->user_id,
                            'administered_by' => auth()->id(),
                            'appointment_id' => $appointment->id,
                            'quantity' => $qty,
                            'administered_at' => now(),
                            'notes' => $request->input('metadata.notes'),
                        ]);
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Consultation saved. Please review and sign to finish this consultation.');
    }

    public function signAndComplete(Request $request, Appointment $appointment)
    {
        $record = $appointment->healthRecord;

        if (!$record || empty($record->vital_signs)) {
            return redirect()->back()->with('error', 'Cannot sign yet. Vital signs are required first.');
        }

        if (empty($record->diagnosis) || empty($record->treatment)) {
            return redirect()->back()->with('error', 'Cannot sign yet. Save diagnosis and treatment first.');
        }

        if (!in_array($appointment->status, ['approved', 'rescheduled'], true)) {
            return redirect()->back()->with('error', 'Only active consultations can be signed and finished.');
        }

        if (!empty($record->metadata['signature']['signed_at'] ?? null)) {
            return redirect()->back()->with('error', 'This consultation is already signed and locked.');
        }

        $request->validate([
            'signer_name' => 'required|string|max:255',
            'signature_data' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error', 'Password confirmation failed. Signature was not applied.');
        }

        if (!str_starts_with($request->signature_data, 'data:image/')) {
            return redirect()->back()->with('error', 'Invalid signature format. Please draw your signature and try again.');
        }

        DB::transaction(function () use ($request, $appointment, $record, $user) {
            $signedAt = now();
            $recordHash = $this->generateRecordFingerprint($record, $signedAt->toIso8601String());

            $metadata = $record->metadata ?? [];
            $metadata['signature'] = [
                'signed_by_id' => $user->id,
                'signed_by_name' => $user->full_name,
                'signed_role' => $user->isMidwife() ? 'Midwife' : 'Doctor',
                'signer_name_confirmed' => $request->signer_name,
                'signed_at' => $signedAt->toIso8601String(),
                'signature_data' => $request->signature_data,
                'facility' => config('app.name'),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'record_id' => $record->id,
                'version' => 1,
                'content_hash' => $recordHash,
            ];

            $record->metadata = $metadata;
            $record->verified_by = $user->id;
            $record->verified_at = $signedAt;
            $record->save();

            $appointment->status = 'completed';
            $appointment->save();
        });

        $routePrefix = $user->isMidwife() ? 'midwife' : 'doctor';
        return redirect()->route($routePrefix . '.appointments.index')->with('success', 'Consultation signed and completed successfully.');
    }

    private function generateRecordFingerprint(HealthRecord $record, string $signedAt): string
    {
        $payload = [
            'record_id' => $record->id,
            'appointment_id' => $record->appointment_id,
            'patient_id' => $record->patient_id,
            'service_id' => $record->service_id,
            'diagnosis' => $record->diagnosis,
            'treatment' => $record->treatment,
            'consultation' => $record->consultation,
            'vital_signs' => $record->vital_signs,
            'signed_at' => $signedAt,
        ];

        return hash('sha256', json_encode($payload, JSON_UNESCAPED_SLASHES));
    }

    /**
     * Manual send: appointment reminder SMS (staff-triggered)
     */
    public function sendReminderSms(Appointment $appointment)
    {
        try {
            $appointment->load(['user.guardian', 'slot']);

            $patient = $appointment->user;
            $target = ($patient && $patient->isDependent() && $patient->guardian)
                ? $patient->guardian
                : $patient;

            if (!$target || empty($target->contact_no)) {
                MessageDispatchLog::create([
                    'appointment_id' => $appointment->id,
                    'patient_user_id' => $patient?->id,
                    'sender_user_id' => auth()->id(),
                    'template_id' => null,
                    'category' => 'appointment_reminder',
                    'channel' => 'sms',
                    'stage' => 1,
                    'trigger_mode' => 'manual',
                    'status' => 'skipped',
                    'reason' => 'missing_contact_number',
                    'sent_at' => now(),
                ]);

                return redirect()->back()->with('error', 'No contact number found for reminder SMS.');
            }

            // Uses appointment_reminder_sms template via notification class
            $target->notify(new UpcomingAppointmentReminder($appointment));

            $templateId = MessageTemplate::query()
                ->where('template_key', 'appointment_reminder_sms')
                ->where('is_active', true)
                ->value('id');

            MessageDispatchLog::create([
                'appointment_id' => $appointment->id,
                'patient_user_id' => $patient?->id,
                'sender_user_id' => auth()->id(),
                'template_id' => $templateId,
                'category' => 'appointment_reminder',
                'channel' => 'sms',
                'stage' => 1,
                'trigger_mode' => 'manual',
                'status' => 'sent',
                'recipient' => $target->contact_no,
                'sent_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Reminder SMS sent successfully.');
        } catch (\Throwable $e) {
            MessageDispatchLog::create([
                'appointment_id' => $appointment->id,
                'patient_user_id' => $appointment->user_id,
                'sender_user_id' => auth()->id(),
                'template_id' => null,
                'category' => 'appointment_reminder',
                'channel' => 'sms',
                'stage' => 1,
                'trigger_mode' => 'manual',
                'status' => 'failed',
                'reason' => 'send_exception',
                'provider_response' => $e->getMessage(),
                'sent_at' => now(),
            ]);

            return redirect()->back()->with('error', 'Failed to send reminder SMS.');
        }
    }

    /**
     * Manual send: appointment reminder email (staff-triggered)
     */
    public function sendReminderEmail(Appointment $appointment)
    {
        try {
            $appointment->load(['user.guardian', 'slot']);

            $patient = $appointment->user;
            $recipient = ($patient && $patient->isDependent() && $patient->guardian)
                ? $patient->guardian->email
                : $patient->email;

            if (empty($recipient)) {
                MessageDispatchLog::create([
                    'appointment_id' => $appointment->id,
                    'patient_user_id' => $patient?->id,
                    'sender_user_id' => auth()->id(),
                    'template_id' => null,
                    'category' => 'appointment_reminder',
                    'channel' => 'email',
                    'stage' => 1,
                    'trigger_mode' => 'manual',
                    'status' => 'skipped',
                    'reason' => 'missing_email',
                    'sent_at' => now(),
                ]);

                return redirect()->back()->with('error', 'No email found for reminder email.');
            }

            $rendered = TemplateService::render('appointment_reminder_email', $appointment);

            Mail::to($recipient)->send(new DefaulterRecallMail(
                $rendered['subject'] ?? 'Appointment Reminder',
                $rendered['body'],
                $recipient
            ));

            MessageDispatchLog::create([
                'appointment_id' => $appointment->id,
                'patient_user_id' => $patient?->id,
                'sender_user_id' => auth()->id(),
                'template_id' => $rendered['template_id'] ?? null,
                'category' => 'appointment_reminder',
                'channel' => 'email',
                'stage' => 1,
                'trigger_mode' => 'manual',
                'status' => 'sent',
                'recipient' => $recipient,
                'sent_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Reminder email sent successfully.');
        } catch (\Throwable $e) {
            MessageDispatchLog::create([
                'appointment_id' => $appointment->id,
                'patient_user_id' => $appointment->user_id,
                'sender_user_id' => auth()->id(),
                'template_id' => null,
                'category' => 'appointment_reminder',
                'channel' => 'email',
                'stage' => 1,
                'trigger_mode' => 'manual',
                'status' => 'failed',
                'reason' => 'send_exception',
                'provider_response' => $e->getMessage(),
                'sent_at' => now(),
            ]);

            return redirect()->back()->with('error', 'Failed to send reminder email.');
        }
    }
}
