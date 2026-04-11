<?php

namespace App\Http\Controllers;

use App\Models\MedicineDistribution;
use App\Models\MedicineSupply;
use App\Models\VaccineAdministration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        // Only admin can manage users
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        return $this->listByRole($request, null);
    }

    public function patients(Request $request)
    {
        return $this->listByRole($request, 'patient');
    }

    public function doctors(Request $request)
    {
        return $this->listByRole($request, 'doctor');
    }

    public function midwives(Request $request)
    {
        return $this->listByRole($request, 'midwife');
    }

    public function healthWorkers(Request $request)
    {
        return $this->listByRole($request, 'health_worker');
    }

    public function admins(Request $request)
    {
        return $this->listByRole($request, 'admin');
    }

    protected function listByRole(Request $request, ?string $fixedRole)
    {
        $query = User::query()->where('id', '!=', auth()->id());

        $role = $fixedRole ?? ($request->filled('role') && $request->role !== 'all' ? $request->role : null);

        if ($role) {
            $query->where('role', $role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());

        $roles = ['admin', 'health_worker', 'midwife', 'patient', 'doctor'];
        $currentRole = $role ?? 'all';

        return view('users.index', compact('users', 'roles', 'currentRole'));
    }

    // -----------------------------
    // Show create user form
    // -----------------------------
    public function create()
    {
        $roles = ['patient', 'health_worker', 'midwife', 'doctor'];
        return view('users.create', compact('roles'));
    }

    // -----------------------------
    // Store new user (must_change_password = true)
    // -----------------------------
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'   => 'required|string|max:255',
            'middle_name'  => 'nullable|string|max:255',
            'last_name'    => 'required|string|max:255',
            'dob'          => 'required|date',
            'gender'       => ['required', Rule::in(['Male','Female','Other'])],
            'address'      => 'required|string|max:255',
            'purok'        => 'required|string|max:255',
            'contact_no'   => 'required|string|max:20',
            'emergency_no' => 'nullable|string|max:20',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:6|confirmed',
            'role'         => ['required', Rule::in(['patient', 'health_worker', 'midwife', 'doctor'])],
            
            // Medical & Additional Info
            'civil_status'               => 'nullable|string|max:50',
            'blood_type'                 => 'nullable|string|max:5',
            'emergency_contact_name'     => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'allergies'                  => 'nullable|string',
            'medical_history'            => 'nullable|string',
            'family_history'             => 'nullable|string',
            'current_medications'        => 'nullable|string',
        ]);

        $validated['must_change_password'] = true;

        $validated['password'] = Hash::make($validated['password']);

        $validated['status'] = true;

        $userData = [
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'dob' => $validated['dob'],
            'gender' => $validated['gender'],
            'address' => $validated['address'],
            'purok' => $validated['purok'],
            'contact_no' => $validated['contact_no'],
            'emergency_no' => $validated['emergency_no'] ?? null,
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'status' => $validated['status'],
            'must_change_password' => $validated['must_change_password'],
        ];

        $medicalData = [
            'civil_status' => $validated['civil_status'] ?? null,
            'blood_type' => $validated['blood_type'] ?? null,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_relationship' => $validated['emergency_contact_relationship'] ?? null,
            'allergies' => $validated['allergies'] ?? null,
            'medical_history' => $validated['medical_history'] ?? null,
            'family_history' => $validated['family_history'] ?? null,
            'current_medications' => $validated['current_medications'] ?? null,
        ];

        $user = User::create($userData);

        if ($user->role === 'patient') {
            $user->patientProfile()->create($medicalData);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully. The user must change their password on first login.');
    }

    // -----------------------------
    // Show user details
    // -----------------------------
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    // -----------------------------
    // Edit user form
    // -----------------------------
    public function edit(User $user)
    {
        $roles = ['patient', 'health_worker', 'midwife', 'admin', 'doctor'];
        return view('users.edit', compact('user', 'roles'));
    }

    // -----------------------------
    // Update user
    // -----------------------------
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name'   => 'required|string|max:255',
            'middle_name'  => 'nullable|string|max:255',
            'last_name'    => 'required|string|max:255',
            'dob'          => 'required|date',
            'gender'       => ['required', Rule::in(['Male','Female','Other'])],
            'address'      => 'required|string|max:255',
            'purok'        => 'required|string|max:255',
            'contact_no'   => 'required|string|max:20',
            'emergency_no' => 'nullable|string|max:20',
            'email'        => ['required','email','max:255', Rule::unique('users')->ignore($user->id)],
            'password'     => 'nullable|string|min:6|confirmed',
            'role'         => ['required', Rule::in(['admin', 'patient', 'health_worker', 'midwife', 'doctor'])],
            'status'       => 'nullable|boolean',

            // Medical & Additional Info
            'civil_status'               => 'nullable|string|max:50',
            'blood_type'                 => 'nullable|string|max:5',
            'emergency_contact_name'     => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'allergies'                  => 'nullable|string',
            'medical_history'            => 'nullable|string',
            'family_history'             => 'nullable|string',
            'current_medications'        => 'nullable|string',
        ]);

        $userData = [
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'dob' => $validated['dob'],
            'gender' => $validated['gender'],
            'address' => $validated['address'],
            'purok' => $validated['purok'],
            'contact_no' => $validated['contact_no'],
            'emergency_no' => $validated['emergency_no'] ?? null,
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        $user->fill($userData);
        
        $user->status = $request->has('status');

        $user->password = $request->password 
            ? Hash::make($request->password)  // Hash new password if provided
            : $user->password;                // Keep existing password if empty

        if ($request->filled('password')) {
            $user->must_change_password = false;
        }

        $user->save();

        $medicalData = [
            'civil_status' => $validated['civil_status'] ?? null,
            'blood_type' => $validated['blood_type'] ?? null,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_relationship' => $validated['emergency_contact_relationship'] ?? null,
            'allergies' => $validated['allergies'] ?? null,
            'medical_history' => $validated['medical_history'] ?? null,
            'family_history' => $validated['family_history'] ?? null,
            'current_medications' => $validated['current_medications'] ?? null,
        ];

        if ($user->role === 'patient') {
            $user->patientProfile()->updateOrCreate(
                [],
                $medicalData
            );
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    // -----------------------------
    // Delete user
    // -----------------------------
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $blockingDependencies = [];

        $dependencyCounts = [
            'appointments' => $user->appointments()->count(),
            'health records' => $user->healthRecords()->count(),
            'created health records' => $user->createdHealthRecords()->count(),
            'patient profile' => $user->patientProfile()->exists() ? 1 : 0,
            'dependents' => $user->dependents()->count(),
            'medicine distributions (patient)' => MedicineDistribution::where('patient_id', $user->id)->count(),
            'medicine distributions (midwife)' => MedicineDistribution::where('midwife_id', $user->id)->count(),
            'vaccine administrations (patient)' => VaccineAdministration::where('patient_id', $user->id)->count(),
            'vaccine administrations (staff)' => VaccineAdministration::where('administered_by', $user->id)->count(),
            'medicine supplies received' => MedicineSupply::where('received_by', $user->id)->count(),
        ];

        foreach ($dependencyCounts as $label => $count) {
            if ($count > 0) {
                $blockingDependencies[] = $label . ': ' . $count;
            }
        }

        if (!empty($blockingDependencies)) {
            return redirect()->back()->with(
                'error',
                'This user cannot be deleted because related medical records exist. Please deactivate the account instead. ' .
                'Blocked records: ' . implode(', ', $blockingDependencies) . '.'
            );
        }

        try {
            $user->delete();
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'This user cannot be deleted because related records still exist. Please deactivate the account instead.');
        }

        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    // -----------------------------
    // Toggle user active/inactive status
    // -----------------------------
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->status = !$user->status;
        $user->save();

        return redirect()->back()->with('success', 'User status updated successfully.');
    }
}
