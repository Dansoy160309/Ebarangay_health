<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use App\Models\PatientProfile;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'guardian_id',
        'relationship',
        'dependency_reason',
        'first_name',
        'middle_name',
        'last_name',
        'dob',
        'gender',
        'address',
        'purok',
        'contact_no',
        'emergency_no',
        'email',
        'password',
        'role',
        'status',
        'must_change_password',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Type casting
     */
    protected $casts = [
        'status' => 'boolean',
        'must_change_password' => 'boolean',
        'email_verified_at' => 'datetime',
        'dob' => 'date',
    ];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = ['full_name', 'age', 'patient_code'];

    /**
     * Automatically hash passwords when set, but avoid double-hashing
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            // Only hash if value is not already hashed
            if (substr($value, 0, 4) !== '$2y$') {
                $this->attributes['password'] = Hash::make($value);
            } else {
                $this->attributes['password'] = $value;
            }
        }
    }

    /**
     * Full name accessor
     */
    public function getFullNameAttribute()
    {
        $name = trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
        return ucwords(strtolower($name));
    }

    /**
     * Age accessor
     */
    public function getAgeAttribute()
    {
        return $this->dob ? $this->dob->age : null;
    }

    /**
     * Patient code accessor (Generated from ID)
     */
    public function getPatientCodeAttribute()
    {
        return 'P-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Relationships
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }

    public function healthRecords()
    {
        return $this->hasMany(HealthRecord::class, 'patient_id');
    }

    public function dependents()
    {
        return $this->hasMany(User::class, 'guardian_id');
    }

    public function guardian()
    {
        return $this->belongsTo(User::class, 'guardian_id');
    }

    public function isDependent()
    {
        return !is_null($this->guardian_id);
    }

    public function patientProfile()
    {
        return $this->hasOne(PatientProfile::class, 'user_id');
    }

    public function createdHealthRecords()
    {
        return $this->hasMany(HealthRecord::class, 'created_by');
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }

    /**
     * Role checks
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isHealthWorker()
    {
        return $this->role === 'health_worker';
    }

    public function isDoctor()
    {
        return $this->role === 'doctor';
    }

    public function isMidwife()
    {
        return $this->role === 'midwife';
    }

    public function isPatient()
    {
        return $this->role === 'patient';
    }

    /**
     * Status check
     */
    public function isActive()
    {
        return $this->status === true;
    }
}
