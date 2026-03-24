<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralSlip extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'midwife_id',
        'date',
        'family_no',
        'referred_from',
        'referred_from_other',
        'referred_to',
        'referred_to_other',
        'pertinent_findings',
        'reason_for_referral',
        'instruction_to_referring_level',
        'actions_taken_by_referred_level',
        'instructions_to_referring_level_final',
    ];

    protected $casts = [
        'date' => 'date',
        'referred_from' => 'array',
        'referred_to' => 'array',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function midwife()
    {
        return $this->belongsTo(User::class, 'midwife_id');
    }
}
