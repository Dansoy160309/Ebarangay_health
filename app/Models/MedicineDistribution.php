<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_id',
        'patient_id',
        'midwife_id',
        'quantity',
        'dosage',
        'frequency',
        'duration',
        'notes',
        'distributed_at',
    ];

    protected $casts = [
        'distributed_at' => 'datetime',
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function midwife()
    {
        return $this->belongsTo(User::class, 'midwife_id');
    }
}

