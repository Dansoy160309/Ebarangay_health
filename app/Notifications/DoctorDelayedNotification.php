<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\DoctorAvailability;

class DoctorDelayedNotification extends Notification
{
    use Queueable;

    protected DoctorAvailability $availability;

    public function __construct(DoctorAvailability $availability)
    {
        $this->availability = $availability;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $doctorName = $this->availability->doctor->full_name;
        $notes = $this->availability->notes ?? 'Doctor is running late.';

        return [
            'message' => "Dr. {$doctorName} is running late today. {$notes} Please wait for further updates.",
            'type' => 'doctor_delayed',
            'doctor_id' => $this->availability->doctor_id,
            'availability_id' => $this->availability->id,
        ];
    }
}