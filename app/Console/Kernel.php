<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Archive old health records daily
        $schedule->command('health:archive-old-records')->daily();

        // Send appointment reminders each morning
        $schedule->command('notifications:send-appointment-reminders')->dailyAt('08:00');

        // Auto-mark no-show and archive stale appointments
        $schedule->command('appointments:auto-no-show')->dailyAt('00:10');

        // Auto-send first defaulter reminder with safeguards
        $schedule->command('notifications:send-defaulter-auto-first-reminders')->hourly();
    }


    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
