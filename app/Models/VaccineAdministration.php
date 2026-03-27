<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaccineAdministration extends Model
{
    use HasFactory;

    protected $fillable = [
        'vaccine_id',
        'vaccine_batch_id',
        'patient_id',
        'administered_by',
        'appointment_id',
        'quantity',
        'administered_at',
        'notes',
    ];

    protected $casts = [
        'administered_at' => 'datetime',
    ];

    public function vaccine()
    {
        return $this->belongsTo(Vaccine::class);
    }

    public function batch()
    {
        return $this->belongsTo(VaccineBatch::class, 'vaccine_batch_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function administeredBy()
    {
        return $this->belongsTo(User::class, 'administered_by');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}

