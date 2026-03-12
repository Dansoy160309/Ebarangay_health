<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Appointment;

class UpcomingAppointmentReminder extends Notification
{
    use Queueable;

    protected Appointment $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'message' => 'Reminder: You have an upcoming appointment for ' .
                ($this->appointment->service ?? 'a health service') .
                ' on ' . $this->appointment->formatted_date .
                ' at ' . $this->appointment->formatted_time . '.',
            'appointment_id' => $this->appointment->id,
            'scheduled_at' => optional($this->appointment->scheduled_at)->toIso8601String(),
            'status' => 'sent',
            'channel' => 'in_app',
            'category' => 'appointment_reminder',
        ];
    }
}

