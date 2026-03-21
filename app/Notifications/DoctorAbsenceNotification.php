<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\DoctorAvailability;

class DoctorAbsenceNotification extends Notification
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
        return [
            'message' => "We apologize, but Dr. {$doctorName} is unavailable today due to an emergency. Our midwife will contact you shortly to reschedule.",
            'type' => 'doctor_absence',
            'doctor_id' => $this->availability->doctor_id,
            'availability_id' => $this->availability->id,
        ];
    }
}
