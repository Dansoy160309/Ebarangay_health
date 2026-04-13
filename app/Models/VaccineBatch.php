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
        'disposed_at',
        'disposed_by',
        'disposed_quantity',
        'disposal_notes',
        'is_active',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'received_at' => 'date',
        'disposed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function vaccine()
    {
        return $this->belongsTo(Vaccine::class);
    }

    public function isExpired()
    {
        return $this->expiry_date->isPast();
    }

    public function isLowStock()
    {
        return $this->quantity_remaining <= 5; // Example threshold
    }

    public function administrations()
    {
        return $this->hasMany(VaccineAdministration::class, 'vaccine_batch_id');
    }

    public function disposer()
    {
        return $this->belongsTo(User::class, 'disposed_by');
    }
}
