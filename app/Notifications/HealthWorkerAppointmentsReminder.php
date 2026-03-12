<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class HealthWorkerAppointmentsReminder extends Notification
{
    use Queueable;

    protected int $appointmentCount;
    protected Carbon $date;

    public function __construct(int $appointmentCount, Carbon $date)
    {
        $this->appointmentCount = $appointmentCount;
        $this->date = $date->copy()->startOfDay();
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $dateLabel = $this->date->isToday()
            ? 'today'
            : ($this->date->isTomorrow() ? 'tomorrow' : $this->date->format('M d, Y'));

        return [
            'message' => 'You have ' . $this->appointmentCount .
                ' appointment(s) scheduled ' . $dateLabel .
                '. Review your Assigned Appointments list for details.',
            'summary_date' => $this->date->toDateString(),
            'appointments_count' => $this->appointmentCount,
            'status' => 'sent',
            'channel' => 'in_app',
            'category' => 'healthworker_reminder',
        ];
    }
}

