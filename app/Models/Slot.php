<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Appointment;

class Slot extends Model
{
    use HasFactory;

    protected $fillable = [
        'service',
        'doctor_id',
        'date',
        'start_time',
        'end_time',
        'capacity',
        'available_spots',
        'is_active',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'date'       => 'date:Y-m-d',
        'start_time' => 'datetime:H:i',
        'end_time'   => 'datetime:H:i',
        'is_active'  => 'boolean',
    ];

    /**
     * Booted events
     */
    protected static function booted()
    {
        static::creating(function ($slot) {
            if ($slot->available_spots === null && $slot->capacity !== null) {
                $slot->available_spots = $slot->capacity;
            }
        });

        static::updating(function ($slot) {
            if ($slot->available_spots === null && $slot->capacity !== null) {
                $slot->available_spots = $slot->capacity;
            }
        });
    }

    /**
     * Relationships
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Count booked appointments excluding cancelled/rejected
     */
    public function bookedCount(): int
    {
        return $this->appointments()
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->count();
    }

    /**
     * Remaining available seats (dynamic)
     */
    public function remainingSeats(): int
    {
        $cap = $this->capacity ?? 0;
        return max($cap - $this->bookedCount(), 0);
    }

    /**
     * Check if slot is full
     */
    public function isFull(): bool
    {
        return $this->remainingSeats() <= 0;
    }

    /**
     * Check if slot date is in the past
     */
    public function isExpired(): bool
    {
        // Combine date and end_time into a single Carbon instance for precise comparison
        $slotEndDateTime = $this->date->copy()->setTimeFrom($this->end_time);
        return $slotEndDateTime->isPast();
    }

    /**
     * Accessor for available spots count
     */
    public function getAvailableAttribute(): int
    {
        return $this->remainingSeats();
    }

    /**
     * Can the slot be booked?
     */
    public function isBookable(): bool
    {
        return $this->is_active && !$this->isFull() && !$this->isExpired();
    }

    /**
     * Nicely formatted time range
     */
    public function displayTime(): string
    {
        $start = $this->start_time ? $this->start_time->format('h:i A') : '—';
        $end = $this->end_time ? $this->end_time->format('h:i A') : null;
        return $end ? "$start - $end" : $start;
    }

    /**
     * Scope: only active slots
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: apply filters
     */
    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['service'])) {
            $query->where('service', 'like', '%' . $filters['service'] . '%');
        }
        if (!empty($filters['date'])) {
            $query->whereDate('date', $filters['date']);
        }
        if (!empty($filters['status'])) {
            $query->where('is_active', $filters['status'] === 'active');
        }
        return $query;
    }

    /**
     * Scope: hide slots already booked by a user
     */
    public function scopeHideBookedBy($query, int $userId)
    {
        $bookedSlotIds = Appointment::where('user_id', $userId)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->pluck('slot_id');

        return $query->whereNotIn('id', $bookedSlotIds);
    }

    /**
     * Human-readable status
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->isExpired()) return 'Expired';
        if ($this->isFull()) return 'Full';
        if (!$this->is_active) return 'Inactive';
        return 'Open';
    }

    /**
     * Status badge color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status_label) {
            'Expired'  => 'gray',
            'Full'     => 'red',
            'Inactive' => 'gray',
            default    => 'green',
        };
    }

    /**
     * For FullCalendar event output
     */
    public function toCalendarEvent(): array
    {
        return [
            'title'           => $this->service,
            'start'           => $this->start_time ? $this->date->format('Y-m-d') . 'T' . $this->start_time->format('H:i:s') : $this->date->toDateString(),
            'end'             => $this->end_time ? $this->date->format('Y-m-d') . 'T' . $this->end_time->format('H:i:s') : null,
            'color'           => $this->status_color,
            'available_spots' => $this->remainingSeats(),
        ];
    }
}
