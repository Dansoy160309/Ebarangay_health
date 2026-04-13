<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineSupply extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_id',
        'batch_number',
        'quantity',
        'expiration_date',
        'supplier_name',
        'date_received',
        'received_by',
        'disposed_at',
        'disposed_by',
        'disposal_notes',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'date_received' => 'date',
        'disposed_at' => 'datetime',
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function disposer()
    {
        return $this->belongsTo(User::class, 'disposed_by');
    }
}

