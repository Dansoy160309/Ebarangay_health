<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Appointment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',         // Patient who booked
        'booked_by',       // Account holder who booked
        'slot_id',         // Slot reference
        'service',         // Service name
        'type',            // 'scheduled' or 'walk-in'
        'scheduled_at',    // Datetime of appointment
        'status',          // pending, approved, rejected, rescheduled, cancelled, no_show, archived
        'patient_name',    // Stored patient name (fallback if user is deleted)
        'patient_email',   // Stored patient email (fallback if user is deleted)
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    // =====================================================
    // 🔗 RELATIONSHIPS
    // =====================================================

    /**
     * Get the user (patient) who booked this appointment.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who booked this appointment (Account Holder).
     */
    public function bookedBy()
    {
        return $this->belongsTo(User::class, 'booked_by');
    }

    /**
     * Get the slot associated with this appointment.
     */
    public function slot()
    {
        return $this->belongsTo(Slot::class, 'slot_id');
    }

    /**
     * Get the health record associated with this appointment.
     */
    public function healthRecord()
    {
        return $this->hasOne(HealthRecord::class)->latestOfMany('id');
    }

    // =====================================================
    // 🧩 ACCESSORS / HELPERS
    // =====================================================

    /**
     * Safely display the patient name.
     */
    public function getDisplayPatientNameAttribute(): string
    {
        return $this->patient_name ?? $this->user?->full_name ?? 'N/A';
    }

    /**
     * Safely display the patient email.
     */
    public function getDisplayPatientEmailAttribute(): string
    {
        return $this->patient_email ?? $this->user?->email ?? 'N/A';
    }

    /**
     * Format the appointment date for display.
     */
    public function getFormattedDateAttribute(): string
    {
        $date = $this->slot?->date ?? $this->scheduled_at;
        return $date ? Carbon::parse($date)->format('M d, Y') : 'N/A';
    }

    /**
     * Format the appointment time for display.
     */
    public function getFormattedTimeAttribute(): string
    {
        if ($this->slot?->start_time) {
            $start = Carbon::parse($this->slot->start_time)->format('g:i A');
            $end = $this->slot->end_time ? Carbon::parse($this->slot->end_time)->format('g:i A') : '';
            return trim("$start - $end");
        }

        return $this->scheduled_at ? $this->scheduled_at->format('g:i A') : 'N/A';
    }

    /**
     * Full datetime display.
     */
    public function getFormattedDateTimeAttribute(): string
    {
        $date = $this->slot?->date ?? $this->scheduled_at;
        $time = $this->slot?->start_time ?? $this->scheduled_at?->format('g:i A');
        return $date ? Carbon::parse($date)->format('M d, Y') . ($time ? " $time" : '') : 'N/A';
    }

    // =====================================================
    // 🔹 STATUS HELPERS
    // =====================================================

    public function isPending(): bool     { return $this->status === 'pending'; }
    public function isApproved(): bool    { return $this->status === 'approved'; }
    public function isRejected(): bool    { return $this->status === 'rejected'; }
    public function isRescheduled(): bool { return $this->status === 'rescheduled'; }
    public function isCancelled(): bool   { return $this->status === 'cancelled'; }
}
    
