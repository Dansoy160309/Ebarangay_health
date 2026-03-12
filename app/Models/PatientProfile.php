<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientProfile extends Model
{
    protected $fillable = [
        'user_id',
        'civil_status',
        'blood_type',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'allergies',
        'medical_history',
        'family_history',
        'current_medications',
        'lmp',
        'edd',
        'gravida',
        'para',
        'is_high_risk',
        'maternal_risk_notes',
        'is_fully_immunized',
        'last_immunization_date',
        'history_miscarriage',
        'previous_complications',
    ];

    protected $casts = [
        'lmp' => 'date',
        'edd' => 'date',
        'is_high_risk' => 'boolean',
        'is_fully_immunized' => 'boolean',
        'last_immunization_date' => 'date',
        'history_miscarriage' => 'boolean',
    ];

    /**
     * Automatically evaluate risk factors (Specifically for Maternal Risk)
     */
    public function evaluateRisk()
    {
        $isHighRisk = false;
        $notes = [];

        // 🤰 Step 1: Only evaluate maternal risk if there is a pregnancy indicator
        // EDD is set and in the future, or LMP is set and within last 42 weeks (normal pregnancy duration)
        $isPregnant = ($this->edd && $this->edd->isFuture()) || 
                      ($this->lmp && $this->lmp->diffInWeeks(now()) < 42);

        if (!$isPregnant) {
            $this->is_high_risk = false;
            $this->maternal_risk_notes = null;
            $this->save();
            return;
        }

        // Age based risk
        if ($this->user->age > 35) {
            $isHighRisk = true;
            $notes[] = "Advanced maternal age (>35)";
        } elseif ($this->user->age < 18) {
            $isHighRisk = true;
            $notes[] = "Adolescent pregnancy (<18)";
        }

        // Gravida based risk
        if ($this->gravida >= 5) {
            $isHighRisk = true;
            $notes[] = "Grand Multiparity (Gravida 5+)";
        }

        // Check medical history for risk keywords
        $riskKeywords = ['hypertension', 'diabetes', 'eclampsia', 'preeclampsia', 'asthma', 'heart'];
        foreach ($riskKeywords as $keyword) {
            if (stripos($this->medical_history, $keyword) !== false) {
                $isHighRisk = true;
                $notes[] = "History of " . ucfirst($keyword);
            }
        }

        $this->is_high_risk = $isHighRisk;
        $this->maternal_risk_notes = implode(', ', $notes);
        $this->save();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

