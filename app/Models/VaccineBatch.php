<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaccineBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'vaccine_id',
        'batch_number',
        'expiry_date',
        'quantity_received',
        'quantity_administered',
        'quantity_remaining',
        'received_at',
        'supplier',
        'is_active',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'received_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function vaccine()
    {        return $this->belongsTo(Vaccine::class);
    }

    public function isExpired()
    {        return $this->expiry_date->isPast();
    }

    public function isLowStock()
    {        return $this->quantity_remaining <= 5; // Example threshold
    }
}
