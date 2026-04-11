<?php

namespace App\Notifications;

use App\Models\Medicine;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MedicineExpiryAlertNotification extends Notification
{
    use Queueable;

    protected Medicine $medicine;
    protected string $status;

    public function __construct(Medicine $medicine, string $status)
    {
        $this->medicine = $medicine;
        $this->status = $status;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $expiryDate = optional($this->medicine->expiration_date)->format('M d, Y') ?? 'Unknown date';

        $message = $this->status === 'expired'
            ? "Expired medicine: {$this->medicine->generic_name} ({$this->medicine->strength}) expired on {$expiryDate}."
            : "Expiring soon medicine: {$this->medicine->generic_name} ({$this->medicine->strength}) expires on {$expiryDate}.";

        return [
            'message' => $message,
            'medicine_id' => $this->medicine->id,
            'expiry_date' => optional($this->medicine->expiration_date)->toDateString(),
            'status' => $this->status,
            'channel' => 'in_app',
            'category' => 'medicine_expiry',
            'route' => route('admin.medicines.index'),
        ];
    }
}
