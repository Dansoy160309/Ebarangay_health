<?php

namespace App\Http\Controllers\HealthWorker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class PatientController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:midwife,health_worker', 'force.password.change']);
    }

    // 🧍‍♀️ View all patients
    public function index(Request $request)
    {
        $type = $request->query('type', 'all');
        $search = $request->query('search');

        $query = Patient::with('guardian');

        if ($type === 'account_holder') {
            $query->whereNull('guardian_id');
        } elseif ($type === 'dependent') {
            $query->whereNotNull('guardian_id');
        }

        // Add search logic (Recommendation 3)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('family_no', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $patients = $query->latest()->paginate(10)->appends(['type' => $type, 'search' => $search]);
        
        return view('healthworker.patients.index', compact('patients', 'type', 'search'));
    }

    // 📄 View patient details
    public function show(Patient $patient)
    {
        abort_unless($patient->role === 'patient', 403);
        // Load related data for the profile view
        $patient->load(['healthRecords' => function($query) {
            $query->latest();
        }, 'appointments' => function($query) {
            $query->latest();
        }, 'guardian', 'dependents']);
        
        return view('healthworker.patients.show', compact('patient'));
    }

    // Get patient details and history for AJAX
    public function getDetails(Patient $patient)
    {
        abort_unless($patient->role === 'patient', 403);
        
        $history = $patient->healthRecords()
            ->with('creator')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($record) {
                return [
                    'id' => $record->id,
                    'created_at' => $record->created_at->format('M d, Y'),
                    'diagnosis' => $record->diagnosis ?? 'N/A',
                    'creator' => $record->creator->full_name ?? 'Unknown',
                    'is_verified' => $record->is_verified,
                ];
            });

        return response()->json([
            'patient' => [
                'full_name' => $patient->full_name,
                'age' => $patient->age,
                'gender' => $patient->gender,
                'address' => $patient->address . ', ' . $patient->purok,
                'family_no' => $patient->family_no,
                'contact_no' => $patient->contact_no,
                'dob' => $patient->dob ? $patient->dob->format('M d, Y') : 'N/A',
                // Added Medical Background fields
                'blood_type' => $patient->blood_type ?? 'N/A',
                'allergies' => $patient->allergies ?? 'None recorded',
                'medical_history' => $patient->medical_history ?? 'None recorded',
                'current_medications' => $patient->current_medications ?? 'None recorded',
                'family_history' => $patient->family_history ?? 'None recorded',
            ],
            'history' => $history
        ]);
    }

    // ➕ Create new patient
    public function create()
    {
        return view('healthworker.patients.create');
    }

    // 🔑 Show Credentials Page
    public function showCredentials()
    {
        if (!session('new_patient_credentials')) {
            return redirect()->route('midwife.patients.index');
        }
        return view('healthworker.patients.credentials');
    }

    public function store(Request $request)
    {
        $rules = [
            'first_name'   => 'required|string|max:255',
            'middle_name'  => 'nullable|string|max:255',
            'last_name'    => 'required|string|max:255',
            'dob'          => 'required|date',
            'gender'       => ['required', Rule::in(['Male', 'Female', 'Other'])],
            'civil_status' => 'nullable|string|max:50',
            'address'      => 'required|string|max:255',
            'purok'        => 'required|string|max:255',
            'family_no'    => 'nullable|string|max:100',
            'contact_no'   => 'required|string|max:20',
            'emergency_no' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            // Medical & Additional Info
            'blood_type'          => 'nullable|string|max:5',
            'allergies'           => 'nullable|string',
            'medical_history'     => 'nullable|string',
            'current_medications' => 'nullable|string',
            'family_history'      => 'nullable|string',
            // Maternal & Immunization
            'lmp'                 => 'nullable|date',
            'edd'                 => 'nullable|date',
            'gravida'             => 'nullable|integer|min:0',
            'para'                => 'nullable|integer|min:0',
            'is_fully_immunized'  => 'nullable|boolean',
        ];

        // Conditional Validation for Account
        if ($request->boolean('no_account')) {
            $rules['email'] = 'nullable'; 
        } else {
            $rules['email'] = 'required|email|unique:users,email';
        }

        $validated = $request->validate($rules);

        $email = '';
        $password = '';
        $generatedPassword = null;
        if ($request->boolean('no_account')) {
            $email = 'walkin_' . \Illuminate\Support\Str::slug($validated['last_name'], '_') . '_' . uniqid() . '@barangay.local';
            $password = Hash::make(\Illuminate\Support\Str::random(16));
        } else {
            $email = $validated['email'];
            // Auto-generate secure random password on backend
            $generatedPassword = $this->generateSecurePassword();
            $password = Hash::make($generatedPassword);
        }

        // Logic for Family Number: Recommendation 1 & 2
        $familyNo = $validated['family_no'] ?? null;
        
        // If it's a dependent, inherit from guardian (Recommendation 1)
        if ($request->filled('guardian_id')) {
            $guardian = User::find($request->guardian_id);
            if ($guardian) {
                $familyNo = $guardian->family_no;
            }
        } 
        // If it's a primary account and no family_no provided, auto-generate (Recommendation 2)
        elseif (!$familyNo) {
            $familyNo = User::generateFamilyNumber();
        }

        $patient = Patient::create([
            'first_name'           => $validated['first_name'],
            'middle_name'          => $validated['middle_name'] ?? null,
            'last_name'            => $validated['last_name'],
            'dob'                  => $validated['dob'],
            'gender'               => $validated['gender'],
            'address'              => $validated['address'],
            'purok'                => $validated['purok'],
            'family_no'            => $familyNo,
            'contact_no'           => $validated['contact_no'],
            'emergency_no'         => $validated['emergency_no'] ?? null,
            'email'                => $email,
            'password'             => $password,
            'status'               => true,
            'must_change_password' => !$request->boolean('no_account'),
            'guardian_id'          => $request->guardian_id ?? null,
            'relationship'         => $request->relationship ?? null,
        ]);

        $profile = $patient->patientProfile()->create([
            'civil_status' => $validated['civil_status'] ?? null,
            'blood_type' => $validated['blood_type'] ?? null,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_relationship' => $validated['emergency_contact_relationship'] ?? null,
            'allergies' => $validated['allergies'] ?? null,
            'medical_history' => $validated['medical_history'] ?? null,
            'current_medications' => $validated['current_medications'] ?? null,
            'family_history' => $validated['family_history'] ?? null,
            'lmp' => $validated['lmp'] ?? null,
            'edd' => $validated['edd'] ?? null,
            'gravida' => $validated['gravida'] ?? null,
            'para' => $validated['para'] ?? null,
            'is_fully_immunized' => $request->boolean('is_fully_immunized'),
        ]);

        $profile->evaluateRisk();

        if ($request->ajax() || $request->has('ajax')) {
            return response()->json([
                'success' => true,
                'patient' => [
                    'id' => $patient->id,
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                    'email' => $patient->email,
                    'dob' => $patient->dob->format('Y-m-d'),
                    'gender' => $patient->gender,
                    'family_no' => $patient->family_no,
                    'contact_no' => $patient->contact_no,
                    'allergies' => $profile->allergies,
                ]
            ]);
        }

        if ($generatedPassword) {
            return redirect()->route('midwife.patients.credentials')
                ->with('success', 'Patient created successfully.')
                ->with('new_patient_credentials', [
                    'email' => $email,
                    'password' => $generatedPassword
                ]);
        }

        return redirect()->route('midwife.patients.index')
            ->with('success', 'Patient created successfully.');
    }

    private function generateSecurePassword($length = 10)
    {
        $uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ'; // Removed ambiguous characters
        $lowercase = 'abcdefghijkmnopqrstuvwxyz';
        $numbers = '23456789';
        
        $password = '';
        $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
        $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
        $password .= $numbers[rand(0, strlen($numbers) - 1)];
        
        $all = $uppercase . $lowercase . $numbers;
        for ($i = 0; $i < $length - 3; $i++) {
            $password .= $all[rand(0, strlen($all) - 1)];
        }
        
        return str_shuffle($password);
    }

    // ✏️ Edit patient
    public function edit(Patient $patient)
    {
        abort_unless($patient->role === 'patient', 403);
        return view('healthworker.patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        abort_unless($patient->role === 'patient', 403);

        $validated = $request->validate([
            'first_name'   => 'required|string|max:255',
            'middle_name'  => 'nullable|string|max:255',
            'last_name'    => 'required|string|max:255',
            'dob'          => 'required|date',
            'gender'       => ['required', Rule::in(['Male', 'Female', 'Other'])],
            'civil_status' => 'nullable|string|max:50',
            'address'      => 'required|string|max:255',
            'purok'        => 'required|string|max:255',
            'family_no'    => 'nullable|string|max:100',
            'contact_no'   => 'required|string|max:20',
            'emergency_no' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'email'        => ['required','email','max:255', Rule::unique('users')->ignore($patient->id)],
            'password'     => 'nullable|string|min:8|confirmed',
            'status'       => 'nullable|boolean',
            'blood_type'          => 'nullable|string|max:5',
            'allergies'           => 'nullable|string',
            'medical_history'     => 'nullable|string',
            'current_medications' => 'nullable|string',
            'family_history'      => 'nullable|string',
            'lmp'                 => 'nullable|date',
            'edd'                 => 'nullable|date',
            'gravida'             => 'nullable|integer|min:0',
            'para'                => 'nullable|integer|min:0',
            'is_fully_immunized'  => 'nullable|boolean',
        ]);

        $patient->fill([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'dob' => $validated['dob'],
            'gender' => $validated['gender'],
            'address' => $validated['address'],
            'purok' => $validated['purok'],
            'family_no' => $validated['family_no'] ?? null,
            'contact_no' => $validated['contact_no'],
            'emergency_no' => $validated['emergency_no'] ?? null,
            'email' => $validated['email'],
        ]);

        // If family_no changed, update all dependents (Recommendation 3)
        if ($patient->isDirty('family_no')) {
            User::where('guardian_id', $patient->id)->update(['family_no' => $patient->family_no]);
        }

        $patient->status = $request->boolean('status', true);

        if ($request->filled('password')) {
            $patient->password = Hash::make($request->password);
            $patient->must_change_password = false;
        }

        $patient->save();

        $profile = $patient->patientProfile()->updateOrCreate(
            [],
            [
                'civil_status' => $validated['civil_status'] ?? null,
                'blood_type' => $validated['blood_type'] ?? null,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
                'emergency_contact_relationship' => $validated['emergency_contact_relationship'] ?? null,
                'allergies' => $validated['allergies'] ?? null,
                'medical_history' => $validated['medical_history'] ?? null,
                'current_medications' => $validated['current_medications'] ?? null,
                'family_history' => $validated['family_history'] ?? null,
                'lmp' => $validated['lmp'] ?? null,
                'edd' => $validated['edd'] ?? null,
                'gravida' => $validated['gravida'] ?? null,
                'para' => $validated['para'] ?? null,
                'is_fully_immunized' => $request->boolean('is_fully_immunized'),
            ]
        );

        $profile->evaluateRisk();

        return redirect()->route('midwife.patients.index')
            ->with('success', 'Patient updated successfully.');
    }

    // ❌ Delete patient
    public function destroy(Patient $patient)
    {
        abort_unless($patient->role === 'patient', 403);
        $patient->delete();
        return back()->with('success', 'Patient deleted successfully.');
    }
}
