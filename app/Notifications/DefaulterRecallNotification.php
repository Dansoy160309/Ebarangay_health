<?php

namespace App\Notifications;

use App\Channels\PhilSmsChannel;
use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DefaulterRecallNotification extends Notification
{
    use Queueable;

    protected Appointment $appointment;
    public $smsType = 'sms_defaulter_recall';

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', PhilSmsChannel::class];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms($notifiable)
    {
        $recipient = $notifiable->contact_no;
        
        // If the notifiable is a dependent, we use the guardian's contact number
        if (empty($recipient) && method_exists($notifiable, 'isDependent') && $notifiable->isDependent()) {
            $recipient = $notifiable->guardian->contact_no ?? null;
        }

        if (empty($recipient)) {
            return [];
        }

        $patientName = $notifiable->first_name;
        $service = $this->appointment->service;
        $date = $this->appointment->scheduled_at 
            ? $this->appointment->scheduled_at->format('M d, Y') 
            : ($this->appointment->slot ? \Carbon\Carbon::parse($this->appointment->slot->date)->format('M d, Y') : 'N/A');

        return [
            'recipient' => $recipient,
            'body'      => "Urgent: {$patientName} missed a critical {$service} appointment on {$date}. Please visit the Barangay Health Center as soon as possible to reschedule.",
        ];
    }

    /**
     * Get the array representation of the notification for database.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'service' => $this->appointment->service,
            'message' => "Recall notice sent for missed {$this->appointment->service} appointment.",
            'type' => 'defaulter_recall',
        ];
    }
}
