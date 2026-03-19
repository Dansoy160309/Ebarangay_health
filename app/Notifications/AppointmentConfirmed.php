<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

use App\Channels\PhilSmsChannel;

class AppointmentConfirmed extends Notification
{
    use Queueable;

    protected $appointment;
    public $smsType = 'sms_appointment_confirmed';

    public function __construct($appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Use database and SMS.
     */
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

        return [
            'recipient' => $notifiable->contact_no,
            'body'      => 'Confirmed: Your appointment for ' . $this->appointment->service . ' on ' . $this->appointment->scheduled_at->format('M d, Y h:i A') . ' is ready. See you there!',
        ];
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
