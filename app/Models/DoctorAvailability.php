<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'date',
        'start_time',
        'end_time',
        'status',
        'actual_arrival_time',
        'is_recurring',
        'recurring_day',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'actual_arrival_time' => 'datetime',
        'is_recurring' => 'boolean',
    ];

    /**
     * Get the doctor that owns the availability.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id')->where('role', 'doctor');
    }
}
