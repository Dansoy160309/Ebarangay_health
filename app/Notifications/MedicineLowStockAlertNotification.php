<?php

namespace App\Notifications;

use App\Models\Medicine;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MedicineLowStockAlertNotification extends Notification
{
    use Queueable;

    protected Medicine $medicine;

    public function __construct(Medicine $medicine)
    {
        $this->medicine = $medicine;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'message' => "Low stock medicine: {$this->medicine->generic_name} ({$this->medicine->strength}) has {$this->medicine->stock} left (reorder at {$this->medicine->reorder_level}).",
            'medicine_id' => $this->medicine->id,
            'stock' => $this->medicine->stock,
            'reorder_level' => $this->medicine->reorder_level,
            'status' => 'low_stock',
            'channel' => 'in_app',
            'category' => 'medicine_low_stock',
            'route' => route('admin.medicines.index'),
        ];
    }
}
