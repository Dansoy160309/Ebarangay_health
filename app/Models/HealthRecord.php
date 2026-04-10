<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthRecord extends Model
{
    use HasFactory;

    protected $fillable = ['patient_id','appointment_id','service_id','created_by','vital_signs','consultation','diagnosis','treatment','immunizations','metadata','status', 'verified_by', 'verified_at', 'created_at'];

    protected $casts = [
        'vital_signs' => 'array',
        'immunizations' => 'array',
        'metadata' => 'array',
        'verified_at' => 'datetime',
    ];

    public function service() { return $this->belongsTo(Service::class); }
    public function appointment() { return $this->belongsTo(Appointment::class); }
    public function patient() { return $this->belongsTo(User::class, 'patient_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function verifier() { return $this->belongsTo(User::class, 'verified_by'); }

    public function getProviderUserAttribute()
    {
        return $this->appointment?->slot?->doctor ?? $this->verifier ?? $this->creator;
    }

    protected function formatProviderName(User $provider): string
    {
        if ($provider->isDoctor()) {
            return 'Dr. ' . $provider->last_name;
        }

        if ($provider->isMidwife()) {
            return 'Midwife ' . $provider->first_name;
        }

        return $provider->full_name;
    }

    public function getProviderNameAttribute(): ?string
    {
        return $this->provider_user ? $this->formatProviderName($this->provider_user) : null;
    }

    public function getIsVerifiedAttribute()
    {
        return !is_null($this->verified_at);
    }
}
