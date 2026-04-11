<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Appointment;
use App\Services\TemplateService;

use App\Channels\PhilSmsChannel;

class UpcomingAppointmentReminder extends Notification
{
    use Queueable;

    protected Appointment $appointment;
    public $smsType = 'sms_appointment_reminders';

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable): array
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

        $body = null;
        try {
            $this->appointment->loadMissing(['user.guardian', 'slot']);
            $rendered = TemplateService::render('appointment_reminder_sms', $this->appointment);
            $body = $rendered['body'] ?? null;
        } catch (\Throwable $e) {
            $body = null;
        }

        if (!$body) {
            $body = 'Reminder: You have an appointment for ' . $this->appointment->service . ' on ' . $this->appointment->formatted_date . ' at ' . $this->appointment->formatted_time . '. Please arrive 15 mins early.';
        }

        return [
            'recipient' => $notifiable->contact_no,
            'body'      => $body,
        ];
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

