<?php

namespace App\Models;

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

    public function getIsVerifiedAttribute()
    {
        return !is_null($this->verified_at);
    }
}
