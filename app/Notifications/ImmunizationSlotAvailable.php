<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Slot;

class ImmunizationSlotAvailable extends Notification
{
    use Queueable;

    protected Slot $slot;

    public function __construct(Slot $slot)
    {
        $this->slot = $slot;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'immunization_slot',
            'message' => 'New Immunization slot available on ' . $this->slot->date->format('M d, Y') . ' ' .
                ($this->slot->start_time ? \Carbon\Carbon::parse($this->slot->start_time)->format('g:i A') : ''),
            'slot_id' => $this->slot->id,
            'date' => $this->slot->date->toDateString(),
            'start_time' => $this->slot->start_time ? \Carbon\Carbon::parse($this->slot->start_time)->format('H:i') : null,
            'end_time' => $this->slot->end_time ? \Carbon\Carbon::parse($this->slot->end_time)->format('H:i') : null,
            'service' => $this->slot->service,
        ];
    }
}
