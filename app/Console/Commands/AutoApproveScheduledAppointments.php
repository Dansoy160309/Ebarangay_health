<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;

class AutoApproveScheduledAppointments extends Command
{
    protected $signature = 'appointments:auto-approve-scheduled';

    protected $description = 'Auto-approve existing pending scheduled appointments booked through patient slots';

    public function handle(): int
    {
        $count = Appointment::where('type', 'scheduled')
            ->where('status', 'pending')
            ->update(['status' => 'approved']);

        $this->info("Updated {$count} scheduled appointments from pending to approved.");

        return static::SUCCESS;
    }
}

