<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AppointmentConfirmed extends Notification
{
    use Queueable;

    protected $appointment;

    public function __construct($appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Use database only (no email).
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Data stored in notifications table.
     */
    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Your appointment for ' . $this->appointment->service .
                         ' on ' . $this->appointment->scheduled_at->format('M d, Y h:i A') .
                         ' has been confirmed.',
            'appointment_id' => $this->appointment->id,
            'status' => $this->appointment->status,
        ];
    }
}
