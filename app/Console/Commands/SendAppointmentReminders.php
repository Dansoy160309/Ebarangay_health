<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\User;
use App\Notifications\UpcomingAppointmentReminder;
use App\Notifications\HealthWorkerAppointmentsReminder;
use Illuminate\Support\Carbon;

class SendAppointmentReminders extends Command
{
    protected $signature = 'notifications:send-appointment-reminders';

    protected $description = 'Send in-app reminders for upcoming appointments to patients and health workers';

    public function handle(): int
    {
        $tomorrowStart = Carbon::tomorrow()->startOfDay();
        $tomorrowEnd = Carbon::tomorrow()->endOfDay();

        $this->info('Sending appointment reminders for ' . $tomorrowStart->toDateString());

        // Patient reminders for appointments scheduled tomorrow
        $appointments = Appointment::with(['user', 'slot'])
            ->whereBetween('scheduled_at', [$tomorrowStart, $tomorrowEnd])
            ->whereIn('status', ['approved', 'rescheduled'])
            ->get();

        $appointments->each(function (Appointment $appointment) {
            $user = $appointment->user;
            if (!$user) {
                return;
            }

            $alreadySent = $user->notifications()
                ->where('type', UpcomingAppointmentReminder::class)
                ->where('data->appointment_id', $appointment->id)
                ->exists();

            if (!$alreadySent) {
                $user->notify(new UpcomingAppointmentReminder($appointment));
            }
        });

        $countForTomorrow = $appointments->count();

        if ($countForTomorrow > 0) {
            $healthWorkers = User::where('role', 'health_worker')->get();

            $healthWorkers->each(function (User $worker) use ($countForTomorrow, $tomorrowStart) {
                $alreadySent = $worker->notifications()
                    ->where('type', HealthWorkerAppointmentsReminder::class)
                    ->where('data->summary_date', $tomorrowStart->toDateString())
                    ->exists();

                if (!$alreadySent) {
                    $worker->notify(new HealthWorkerAppointmentsReminder($countForTomorrow, $tomorrowStart));
                }
            });
        }

        $this->info('Appointment reminders processed. Patient reminders: ' . $appointments->count() .
            ', health worker alerts: ' . ($countForTomorrow > 0 ? 'sent' : 'none (no appointments)'));

        return self::SUCCESS;
    }
}

