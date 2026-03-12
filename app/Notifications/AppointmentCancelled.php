<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AppointmentCancelled extends Notification
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
        $statusText = $this->appointment->status === 'rejected' ? 'rejected' : 'cancelled';
        return [
            'message' => 'Your appointment for ' . $this->appointment->service .
                         ' on ' . $this->appointment->scheduled_at->format('M d, Y h:i A') .
                         " has been {$statusText}.",
            'appointment_id' => $this->appointment->id,
            'status' => $this->appointment->status,
        ];
    }
}
