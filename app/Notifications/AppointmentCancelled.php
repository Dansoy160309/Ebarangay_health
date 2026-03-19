<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

use App\Channels\PhilSmsChannel;

class AppointmentCancelled extends Notification
{
    use Queueable;

    protected $appointment;
    public $smsType = 'sms_appointment_cancelled';

    public function __construct($appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable)
    {
        return ['database', PhilSmsChannel::class];
    }

    /**
     * Build the SMS representation of the notification.
     */
    public function toSms($notifiable)
    {
        if (empty($notifiable->contact_no)) {
            return [];
        }

        $statusText = $this->appointment->status === 'rejected' ? 'rejected' : 'cancelled';
        return [
            'recipient' => $notifiable->contact_no,
            'body'      => "Update: Your appointment for {$this->appointment->service} on " . $this->appointment->scheduled_at->format('M d, Y h:i A') . " has been {$statusText}. Thank you.",
        ];
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
