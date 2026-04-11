<?php

namespace App\Notifications;

use App\Models\Vaccine;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VaccineLowStockAlertNotification extends Notification
{
    use Queueable;

    protected Vaccine $vaccine;

    public function __construct(Vaccine $vaccine)
    {
        $this->vaccine = $vaccine;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'message' => "Low stock vaccine: {$this->vaccine->name} has {$this->vaccine->in_stock_quantity} doses left (min {$this->vaccine->min_stock_level}).",
            'vaccine_id' => $this->vaccine->id,
            'stock' => $this->vaccine->in_stock_quantity,
            'min_stock_level' => $this->vaccine->min_stock_level,
            'status' => 'low_stock',
            'channel' => 'in_app',
            'category' => 'vaccine_low_stock',
            'route' => route('admin.vaccines.index'),
        ];
    }
}
