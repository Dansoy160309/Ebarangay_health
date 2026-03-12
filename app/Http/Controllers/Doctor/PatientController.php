<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'all');

        $query = Patient::with('guardian');

        if ($type === 'account_holder') {
            $query->whereNull('guardian_id');
        } elseif ($type === 'dependent') {
            $query->whereNotNull('guardian_id');
        }

        $patients = $query->latest()->paginate(10)->appends(['type' => $type]);

        return view('doctor.patients.index', compact('patients', 'type'));
    }

    public function show(Patient $patient)
    {
        abort_unless($patient->role === 'patient', 403);

        $patient->load(['guardian', 'dependents', 'patientProfile']);

        return view('doctor.patients.show', compact('patient'));
    }
}

