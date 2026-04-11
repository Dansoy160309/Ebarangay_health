<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\MessageDispatchLog;
use App\Models\SmsSetting;
use App\Notifications\DefaulterRecallNotification;
use App\Services\ReminderPolicyService;
use App\Services\TemplateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendDefaulterAutoFirstReminder extends Command
{
    protected $signature = 'notifications:send-defaulter-auto-first-reminders';

    protected $description = 'Send automatic first SMS reminder to defaulters with safeguards and audit logs';

    public function handle(ReminderPolicyService $policy): int
    {
        if (!SmsSetting::isEnabled('sms_enabled')) {
            $this->info('Skipped: global SMS is disabled.');
            return self::SUCCESS;
        }

        if (!SmsSetting::isEnabled('sms_auto_defaulter_first_reminder')) {
            $this->info('Skipped: auto defaulter first reminder is disabled.');
            return self::SUCCESS;
        }

        if (!SmsSetting::isEnabled('sms_defaulter_recall')) {
            $this->info('Skipped: defaulter recall SMS is disabled.');
            return self::SUCCESS;
        }

        $appointments = Appointment::with(['user.guardian', 'slot'])
            ->where('status', 'no_show')
            ->where(function ($q) {
                $q->where('service', 'like', '%Immunization%')
                  ->orWhere('service', 'like', '%Prenatal%');
            })
            ->get();

        $sent = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($appointments as $appointment) {
            $patient = $appointment->user;
            if (!$patient) {
                $this->logSkipped($appointment, null, 'sms', 1, 'auto', 'missing_patient');
                $skipped++;
                continue;
            }

            $recipient = $patient->contact_no;
            if (empty($recipient) && $patient->isDependent()) {
                $recipient = optional($patient->guardian)->contact_no;
            }

            if (empty($recipient)) {
                $this->logSkipped($appointment, $patient->id, 'sms', 1, 'auto', 'missing_contact_number');
                $skipped++;
                continue;
            }

            $policyCheck = $policy->canAutoSend($appointment, 'sms', 1);
            if (!$policyCheck['allow']) {
                $this->logSkipped($appointment, $patient->id, 'sms', 1, 'auto', $policyCheck['reason']);
                $skipped++;
                continue;
            }

            try {
                $rendered = TemplateService::render('defaulter_recall_sms', $appointment);

                $patient->notify(new DefaulterRecallNotification($appointment, $rendered['body']));

                MessageDispatchLog::create([
                    'appointment_id' => $appointment->id,
                    'patient_user_id' => $patient->id,
                    'sender_user_id' => null,
                    'template_id' => $rendered['template_id'] ?? null,
                    'category' => ReminderPolicyService::CATEGORY,
                    'channel' => 'sms',
                    'stage' => 1,
                    'trigger_mode' => 'auto',
                    'status' => 'sent',
                    'recipient' => $recipient,
                    'reason' => null,
                    'sent_at' => now(),
                ]);

                $sent++;
            } catch (\Throwable $e) {
                Log::error('Auto defaulter SMS failed', [
                    'appointment_id' => $appointment->id,
                    'patient_id' => $patient->id,
                    'error' => $e->getMessage(),
                ]);

                MessageDispatchLog::create([
                    'appointment_id' => $appointment->id,
                    'patient_user_id' => $patient->id,
                    'sender_user_id' => null,
                    'template_id' => null,
                    'category' => ReminderPolicyService::CATEGORY,
                    'channel' => 'sms',
                    'stage' => 1,
                    'trigger_mode' => 'auto',
                    'status' => 'failed',
                    'recipient' => $recipient,
                    'reason' => 'send_exception',
                    'provider_response' => $e->getMessage(),
                    'sent_at' => now(),
                ]);

                $failed++;
            }
        }

        $this->info("Auto defaulter reminders processed: sent={$sent}, skipped={$skipped}, failed={$failed}");

        return self::SUCCESS;
    }

    private function logSkipped(
        Appointment $appointment,
        ?int $patientUserId,
        string $channel,
        int $stage,
        string $triggerMode,
        string $reason
    ): void {
        MessageDispatchLog::create([
            'appointment_id' => $appointment->id,
            'patient_user_id' => $patientUserId,
            'sender_user_id' => null,
            'template_id' => null,
            'category' => ReminderPolicyService::CATEGORY,
            'channel' => $channel,
            'stage' => $stage,
            'trigger_mode' => $triggerMode,
            'status' => 'skipped',
            'recipient' => null,
            'reason' => $reason,
            'sent_at' => now(),
        ]);
    }
}
