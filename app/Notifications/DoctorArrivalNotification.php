<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\DoctorAvailability;

class DoctorArrivalNotification extends Notification
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
            'message' => "Dr. {$doctorName} has arrived at the health center and is now ready for consultations.",
            'type' => 'doctor_arrival',
            'doctor_id' => $this->availability->doctor_id,
            'availability_id' => $this->availability->id,
        ];
    }
}
