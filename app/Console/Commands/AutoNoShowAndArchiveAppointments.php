<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use Illuminate\Support\Carbon;

class AutoNoShowAndArchiveAppointments extends Command
{
    protected $signature = 'appointments:auto-no-show';

    protected $description = 'Mark unattended appointments as no_show and archive after 3 days';

    public function handle(): int
    {
        $now = Carbon::now();

        // 1) Mark past, still-active appointments as no_show
        $noShowCandidates = Appointment::query()
            ->whereIn('status', ['approved', 'rescheduled'])
            ->where(function ($q) use ($now) {
                // Use slot date if present, otherwise scheduled_at
                $q->whereHas('slot', function ($sq) use ($now) {
                    $sq->whereDate('date', '<', $now->toDateString());
                })
                ->orWhere(function ($qq) use ($now) {
                    $qq->whereNull('slot_id')
                       ->whereNotNull('scheduled_at')
                       ->where('scheduled_at', '<', $now->startOfDay());
                });
            })
            ->get();

        $noShowUpdated = 0;
        foreach ($noShowCandidates as $appt) {
            $appt->update(['status' => 'no_show']);
            $noShowUpdated++;
        }

        // 2) Archive no_show appointments after 3 days
        $threshold = $now->copy()->subDays(3);
        $archiveCandidates = Appointment::query()
            ->where('status', 'no_show')
            ->where(function ($q) use ($threshold) {
                $q->whereHas('slot', function ($sq) use ($threshold) {
                    $sq->whereDate('date', '<=', $threshold->toDateString());
                })
                ->orWhere(function ($qq) use ($threshold) {
                    $qq->whereNull('slot_id')
                       ->whereNotNull('scheduled_at')
                       ->where('scheduled_at', '<=', $threshold);
                });
            })
            ->get();

        $archived = 0;
        foreach ($archiveCandidates as $appt) {
            $appt->update(['status' => 'archived']);
            $archived++;
        }

        $this->info("No-show updated: {$noShowUpdated} | Archived: {$archived}");
        return Command::SUCCESS;
    }
}

