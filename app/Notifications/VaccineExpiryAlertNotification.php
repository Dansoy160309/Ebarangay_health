<?php

namespace App\Notifications;

use App\Models\VaccineBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VaccineExpiryAlertNotification extends Notification
{
    use Queueable;

    protected VaccineBatch $batch;
    protected string $status;

    public function __construct(VaccineBatch $batch, string $status)
    {
        $this->batch = $batch;
        $this->status = $status;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $vaccineName = $this->batch->vaccine?->name ?? 'Vaccine';
        $expiryDate = optional($this->batch->expiry_date)->format('M d, Y') ?? 'Unknown date';

        $message = $this->status === 'expired'
            ? "Expired vaccine batch: {$vaccineName} ({$this->batch->batch_number}) expired on {$expiryDate}."
            : "Expiring soon vaccine batch: {$vaccineName} ({$this->batch->batch_number}) expires on {$expiryDate}.";

        return [
            'message' => $message,
            'vaccine_batch_id' => $this->batch->id,
            'vaccine_id' => $this->batch->vaccine_id,
            'batch_number' => $this->batch->batch_number,
            'expiry_date' => optional($this->batch->expiry_date)->toDateString(),
            'status' => $this->status,
            'channel' => 'in_app',
            'category' => 'vaccine_expiry',
            'route' => route('admin.vaccines.index'),
        ];
    }
}
