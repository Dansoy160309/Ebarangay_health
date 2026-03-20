<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HealthRecord;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Slot;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HealthRecordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,health_worker,doctor,midwife')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $query = HealthRecord::with(['patient', 'creator', 'verifier', 'service']);

        // Search Scope
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Search by Patient Name
                $q->whereHas('patient', function($p) use ($search) {
                    $p->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('middle_name', 'like', "%{$search}%")
                      ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                })
                // Search by Diagnosis, Symptoms, etc.
                ->orWhere('diagnosis', 'like', "%{$search}%")
                ->orWhere('consultation', 'like', "%{$search}%")
                ->orWhere('vital_signs', 'like', "%{$search}%")
                ->orWhere('treatment', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            if ($request->status === 'verified') {
                $query->whereNotNull('verified_at');
            } elseif ($request->status === 'pending') {
                $query->whereNull('verified_at');
            }
        }

        // Service Filter
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        // Date Range Filter
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        if ($user->isPatient()) {
            $dependents = $user->dependents;
            
            // Determine whose records to show (Default: Account Holder)
            $subjectId = $request->input('subject_id', $user->id);
            
            // Authorization: Ensure subject is self or dependent
            $canView = ($subjectId == $user->id) || $dependents->contains('id', $subjectId);
            
            if (!$canView) {
                abort(403, 'Unauthorized access to these records.');
            }
            
            $selectedSubject = User::find($subjectId);
            
            $records = $query->where('patient_id', $subjectId)
                             ->latest()
                             ->paginate(10)
                             ->appends($request->all());

            return view('patient.health_records.index', compact('records', 'dependents', 'selectedSubject'));
        }

        if ($user->isHealthWorker() || $user->isMidwife()) {
            $searchResults = collect();
            $selectedPatient = null;
            $selectedSubject = null;
            $records = null; // Default null to indicate no selection

            // 1. Search for Patients (Primary Accounts and Dependents)
            if ($request->filled('search_patient')) {
                $term = $request->search_patient;
                $searchResults = User::where('role', 'patient')
                    ->where(function($q) use ($term) {
                        $q->where('first_name', 'like', "%{$term}%")
                          ->orWhere('last_name', 'like', "%{$term}%")
                          ->orWhere('middle_name', 'like', "%{$term}%")
                          ->orWhere('email', 'like', "%{$term}%")
                          ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$term}%")
                          ->orWhere(DB::raw("CONCAT(first_name, ' ', middle_name, ' ', last_name)"), 'like', "%{$term}%");
                    })
                    ->with('guardian')
                    ->take(15)
                    ->get();
            }

            // 2. Select Subject directly (Treat everyone as individual)
            if ($request->filled('subject_id')) {
                $subjectId = $request->subject_id;
                $selectedSubject = Patient::find($subjectId);
                
                if ($selectedSubject) {
                    $records = HealthRecord::with(['creator', 'verifier', 'patient'])
                       ->where('patient_id', $subjectId)
                       ->latest()
                       ->paginate(10)
                       ->appends($request->all());
                }
            } elseif ($request->filled('selected_patient_id')) {
                 // Fallback for legacy links: if selected_patient_id is used, treat it as subject_id
                 $subjectId = $request->selected_patient_id;
                 $selectedSubject = User::find($subjectId);
                 
                 if ($selectedSubject) {
                     $records = HealthRecord::with(['creator', 'verifier', 'patient'])
                        ->where('patient_id', $subjectId)
                        ->latest()
                        ->paginate(10)
                        ->appends($request->all());
                 }
            }

            $routePrefix = $user->isMidwife() ? 'midwife' : 'healthworker';

            return view('healthworker.health_records.index', compact('records', 'searchResults', 'selectedPatient', 'selectedSubject', 'routePrefix'));
        }
        
        $records = $query->latest()->paginate(15);
        
        if ($user->isAdmin()) {
            $searchResults = collect();
            $selectedPatient = null;
            $selectedSubject = null;
            $records = null;

            // 1. Search for Patients (Primary Accounts and Dependents)
            if ($request->filled('search_patient')) {
                $term = $request->search_patient;
                $searchResults = User::where('role', 'patient')
                    ->where(function($q) use ($term) {
                        $q->where('first_name', 'like', "%{$term}%")
                          ->orWhere('last_name', 'like', "%{$term}%")
                          ->orWhere('middle_name', 'like', "%{$term}%")
                          ->orWhere('email', 'like', "%{$term}%")
                          ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$term}%")
                          ->orWhere(DB::raw("CONCAT(first_name, ' ', middle_name, ' ', last_name)"), 'like', "%{$term}%");
                    })
                    ->with('guardian')
                    ->take(15)
                    ->get();
            }

            // 2. Select Subject directly
            if ($request->filled('subject_id')) {
                $subjectId = $request->subject_id;
                $selectedSubject = Patient::find($subjectId);
                
                if ($selectedSubject) {
                    $records = HealthRecord::with(['creator', 'verifier', 'patient'])
                       ->where('patient_id', $subjectId)
                       ->latest()
                       ->paginate(10)
                       ->appends($request->all());
                }
            }

            return view('admin.health_records.index', compact('records', 'searchResults', 'selectedPatient', 'selectedSubject'));
        }
        
        if ($user->isDoctor()) {
            $services = Service::all();
            return view('doctor.health_records.index', compact('records', 'services'));
        }

        return view('health_records.index', compact('records'));
    }

    public function create(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $selectedPatient = null;
        if ($request->has('patient_id')) {
            $selectedPatient = User::where('role', 'patient')->find($request->patient_id);
        }

        if ($user->isAdmin()) {
            abort(403, 'Admins have read-only access to health records.');
        }

        if ($user->isDoctor() || $user->isMidwife()) {
            $patients = Patient::all();
            $services = Service::all();
            return view('doctor.health_records.create', compact('patients', 'selectedPatient', 'services'));
        }
        
        // For Health Workers, we enforce selecting a patient first, so we don't pass the full list.
        $services = Service::all();
        $doctors = Doctor::all();
        return view('healthworker.health_records.create', compact('selectedPatient', 'services', 'doctors'));
    }

    public function store(Request $request)
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
             abort(403, 'Admins have read-only access to health records.');
        }

        $request->validate([
            'patient_id'=>'required|exists:users,id',
            'service_id'=>'nullable|exists:services,id',
            'vital_signs'=>'nullable|array',
            'consultation'=>'nullable|string',
            'diagnosis'=>'nullable|string',
            'treatment'=>'nullable|string',
            'immunizations'=>'nullable|array',
            'metadata'=>'nullable|array',
            'pass_to_doctor' => 'nullable|boolean',
            'doctor_id' => 'required_if:pass_to_doctor,true|nullable|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'created_at' => 'nullable|date',
        ]);

        $createdAt = $request->filled('created_at') ? Carbon::parse($request->created_at) : now();

        $record = HealthRecord::create([
            'patient_id'=>$request->patient_id,
            'service_id'=>$request->service_id,
            'created_by'=>auth()->id(),
            'vital_signs'=>$request->vital_signs,
            'consultation'=>$request->consultation,
            'diagnosis'=>$request->diagnosis,
            'treatment'=>$request->treatment,
            'immunizations'=>$request->immunizations,
            'metadata'=>$request->metadata,
            'status'=>'active',
            'verified_at' => $createdAt, // Automatically verified
            'verified_by' => auth()->id(), // Verified by creator (Health Worker)
            'created_at' => $createdAt,
        ]);

        // 🤰 Sync Prenatal Metadata to Patient Profile if applicable
        if ($request->has('metadata.lmp') || $request->has('metadata.gravida')) {
            $patient = User::find($request->patient_id);
            if ($patient && $patient->patientProfile) {
                $patient->patientProfile->update([
                    'lmp' => $request->input('metadata.lmp'),
                    'edd' => $request->input('metadata.edd'),
                    'gravida' => $request->input('metadata.gravida'),
                    'para' => $request->input('metadata.para'),
                    'history_miscarriage' => $request->boolean('metadata.history_miscarriage'),
                    'previous_complications' => $request->input('metadata.previous_complications'),
                ]);
                $patient->patientProfile->evaluateRisk();
            }
        }

        // Handle "Pass to Doctor" workflow
        if ($request->boolean('pass_to_doctor')) {
            $appointment = null;

            if ($request->filled('appointment_id')) {
                $appointment = Appointment::find($request->appointment_id);
                if ($appointment) {
                    $appointment->update(['status' => 'approved']);
                }
            } else {
                // Create new Walk-in Appointment
                // Find an active slot for the doctor today
                $slot = Slot::where('doctor_id', $request->doctor_id)
                            ->whereDate('date', Carbon::today())
                            ->where('is_active', true)
                            ->first();

                if ($slot) {
                    $serviceName = Service::find($request->service_id)?->name ?? 'Consultation';
                    $appointment = Appointment::create([
                        'user_id' => $request->patient_id,
                        'slot_id' => $slot->id,
                        'service' => $serviceName,
                        'type' => 'walk-in',
                        'status' => 'approved',
                        'scheduled_at' => now(),
                    ]);
                } else {
                    // If no slot found, we might want to warn the user, but we already created the health record.
                    // Let's just flash a warning.
                    session()->flash('warning', 'Health record created, but could not pass to doctor: Doctor has no active slots today.');
                }
            }

            if ($appointment) {
                $record->update(['appointment_id' => $appointment->id]);
            }
        }

        return redirect()->route($this->getRedirectRoute())->with('success','Health record created.');
    }

    public function show($id)
    {
        $record = HealthRecord::with(['patient', 'creator', 'verifier'])->findOrFail($id);
        
        // Authorization check
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        if ($user->isPatient()) {
            $isOwnRecord = $record->patient_id === $user->id;
            $isDependentRecord = $user->dependents()->where('id', $record->patient_id)->exists();
            
            if (!$isOwnRecord && !$isDependentRecord) {
                abort(403);
            }
        }

        if ($user->isPatient()) {
            return view('patient.health_records.show', compact('record'));
        }
        if ($user->isAdmin()) {
            return view('admin.health_records.show', compact('record'));
        }
        if ($user->isDoctor() || $user->isMidwife()) {
            return view('doctor.health_records.show', compact('record'));
        }
        if ($user->isHealthWorker()) {
            return view('healthworker.health_records.show', compact('record'));
        }

        return view('health_records.show', compact('record'));
    }

    public function edit($id)
    {
        $record = HealthRecord::findOrFail($id);
        
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user->isAdmin()) {
             abort(403, 'Admins have read-only access to health records.');
        }
        
        if (!$user->isHealthWorker() && !$user->isDoctor() && !$user->isMidwife()) {
             abort(403);
        }
        
        $patients = Patient::all();
        $services = Service::all();
        
        if ($user->isAdmin()) {
            return view('admin.health_records.edit', compact('record', 'patients', 'services'));
        }
        if ($user->isDoctor() || $user->isMidwife()) {
            return view('doctor.health_records.edit', compact('record', 'patients', 'services'));
        }
        return view('healthworker.health_records.edit', compact('record', 'patients', 'services'));
    }

    public function update(Request $request, $id)
    {
        $record = HealthRecord::findOrFail($id);
        
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user->isAdmin()) {
             abort(403, 'Admins have read-only access to health records.');
        }

        if (!$user->isHealthWorker() && !$user->isDoctor() && !$user->isMidwife()) {
             abort(403);
        }
        
        $request->validate([
            'patient_id'=>'required|exists:users,id',
            'service_id'=>'nullable|exists:services,id',
            'vital_signs'=>'nullable|array',
            'consultation'=>'nullable|string',
            'diagnosis'=>'nullable|string',
            'treatment'=>'nullable|string',
            'immunizations'=>'nullable|array',
            'metadata'=>'nullable|array',
            'status' => 'required|in:active,archived',
            'created_at' => 'required|date',
        ]);

        $record->update([
            'patient_id'=>$request->patient_id,
            'service_id'=>$request->service_id,
            'vital_signs'=>$request->vital_signs,
            'consultation'=>$request->consultation,
            'diagnosis'=>$request->diagnosis,
            'treatment'=>$request->treatment,
            'immunizations'=>$request->immunizations,
            'metadata'=>$request->metadata,
            'status'=>$request->status,
            'created_at'=>$request->created_at,
        ]);

        return redirect()->route($this->getRedirectRoute())->with('success','Health record updated.');
    }

    public function verify($id)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user->isAdmin()) {
            abort(403, 'Admins have read-only access to health records.');
        }

        if (!$user->isAdmin()) {
            abort(403, 'Only admins can verify records.');
        }
        
        $record = HealthRecord::findOrFail($id);
        $record->update([
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);
        
        return back()->with('success', 'Record verified successfully.');
    }

    private function getRedirectRoute() {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if ($user->isAdmin()) return 'admin.health-records.index';
        if ($user->isDoctor()) return 'doctor.health-records.index';
        if ($user->isMidwife()) return 'midwife.health-records.index';
        if ($user->isHealthWorker()) return 'healthworker.health-records.index';
        return 'patient.health-records.index';
    }
}
