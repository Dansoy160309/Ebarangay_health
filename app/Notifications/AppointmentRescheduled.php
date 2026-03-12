<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AppointmentRescheduled extends Notification
{
    use Queueable;

    protected $appointment;

    public function __construct($appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Your appointment for ' . $this->appointment->service .
                         ' has been rescheduled to ' . $this->appointment->scheduled_at->format('M d, Y h:i A') . '.',
            'appointment_id' => $this->appointment->id,
            'status' => $this->appointment->status,
        ];
    }
}
