<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\MessageDispatchLog;
use Carbon\Carbon;

class ReminderPolicyService
{
    public const CATEGORY = 'defaulter_recall';

    /**
     * Evaluate whether an automatic reminder can be sent.
     */
    public function canAutoSend(Appointment $appointment, string $channel, int $stage = 1): array
    {
        if ($appointment->status !== 'no_show') {
            return ['allow' => false, 'reason' => 'appointment_resolved'];
        }

        if ($stage !== 1) {
            return ['allow' => false, 'reason' => 'auto_first_stage_only'];
        }

        if ($this->isQuietHours()) {
            return ['allow' => false, 'reason' => 'quiet_hours'];
        }

        if ($this->isDailyLimitReached()) {
            return ['allow' => false, 'reason' => 'daily_limit_reached'];
        }

        // Duplicate protection: block if already sent same stage/channel for this appointment
        $alreadySent = MessageDispatchLog::query()
            ->where('appointment_id', $appointment->id)
            ->where('category', self::CATEGORY)
            ->where('channel', $channel)
            ->where('stage', $stage)
            ->where('status', 'sent')
            ->exists();

        if ($alreadySent) {
            return ['allow' => false, 'reason' => 'duplicate_message'];
        }

        return ['allow' => true, 'reason' => null];
    }

    public function nextManualStage(int $appointmentId): int
    {
        $sentCount = MessageDispatchLog::query()
            ->where('appointment_id', $appointmentId)
            ->where('category', self::CATEGORY)
            ->where('status', 'sent')
            ->count();

        return $sentCount + 1;
    }

    public function isDailyLimitReached(): bool
    {
        $dailyLimit = (int) config('messaging.auto_defaulter_first_reminder.daily_limit', 100);

        $todayCount = MessageDispatchLog::query()
            ->where('category', self::CATEGORY)
            ->where('trigger_mode', 'auto')
            ->where('status', 'sent')
            ->whereDate('created_at', Carbon::today())
            ->count();

        return $todayCount >= $dailyLimit;
    }

    public function isQuietHours(): bool
    {
        $start = config('messaging.auto_defaulter_first_reminder.quiet_hours_start', '21:00');
        $end = config('messaging.auto_defaulter_first_reminder.quiet_hours_end', '06:00');

        $nowMinutes = Carbon::now()->hour * 60 + Carbon::now()->minute;

        [$startHour, $startMinute] = array_map('intval', explode(':', $start));
        [$endHour, $endMinute] = array_map('intval', explode(':', $end));

        $startMinutes = ($startHour * 60) + $startMinute;
        $endMinutes = ($endHour * 60) + $endMinute;

        // Overnight quiet window, e.g., 21:00 -> 06:00
        if ($endMinutes <= $startMinutes) {
            return $nowMinutes >= $startMinutes || $nowMinutes < $endMinutes;
        }

        return $nowMinutes >= $startMinutes && $nowMinutes < $endMinutes;
    }
}
