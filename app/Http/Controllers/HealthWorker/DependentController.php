<?php

namespace App\Http\Controllers\HealthWorker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DependentController extends Controller
{
    /**
     * Store a newly created dependent in storage.
     */
    public function store(Request $request, User $patient)
    {
        // Validate request
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'required|date|before:today',
            'gender' => 'required|in:Male,Female,Other',
            'relationship' => 'required|string|max:255',
            'dependency_reason' => 'nullable|string|max:255',
            'blood_type' => 'nullable|string|max:5',
            'allergies' => 'nullable|string',
            'existing_conditions' => 'nullable|string',
        ]);

        $dummyEmail = 'dependent_' . time() . '_' . Str::random(5) . '@barangay.local';

        $dependent = User::create([
            'guardian_id' => $patient->id,
            'relationship' => $validated['relationship'],
            'dependency_reason' => $validated['dependency_reason'],
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],
            'dob' => $validated['dob'],
            'gender' => $validated['gender'],
            'address' => $patient->address, // Inherit address from guardian
            'purok' => $patient->purok,     // Inherit purok from guardian
            'contact_no' => $patient->contact_no, // Inherit contact from guardian
            'emergency_no' => $patient->contact_no, // Guardian is the emergency contact
            'email' => $dummyEmail,
            'password' => Hash::make(Str::random(32)), // Random password to prevent login
            'role' => 'patient', // They are patients effectively
            'status' => true,
        ]);

        $dependent->patientProfile()->create([
            'emergency_contact_name' => $patient->full_name,
            'emergency_contact_relationship' => 'Guardian',
            'blood_type' => $validated['blood_type'] ?? null,
            'allergies' => $validated['allergies'] ?? null,
            'medical_history' => $validated['existing_conditions'] ?? null,
        ]);

        return redirect()->route('healthworker.patients.show', $patient->id)
            ->with('success', 'Dependent added successfully.');
    }

    /**
     * Remove the specified dependent from storage.
     */
    public function destroy(User $patient, User $dependent)
    {
        // Verify relationship
        if ($dependent->guardian_id !== $patient->id) {
            abort(403, 'This user is not a dependent of the specified patient.');
        }

        // Delete the dependent
        $dependent->delete();

        return redirect()->back()->with('success', 'Dependent removed successfully.');
    }
}
