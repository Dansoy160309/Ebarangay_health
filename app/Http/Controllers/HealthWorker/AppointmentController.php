<?php

namespace App\Http\Controllers\HealthWorker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Slot;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Notifications\AppointmentConfirmed;

use App\Notifications\AppointmentRescheduled;
use App\Notifications\AppointmentCancelled;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments
     */
    public function index(Request $request)
    {
        // Default to scheduled type if not set
        if (!$request->has('type')) {
            $request->merge(['type' => 'scheduled']);
        }

        // Default to today if date not set and not looking for vitals specifically
        if (!$request->filled('date') && !$request->has('vitals_only')) {
            $request->merge(['date' => Carbon::today()->toDateString()]);
        }

        $query = Appointment::with(['user', 'slot', 'healthRecord'])
            ->orderBy('scheduled_at', 'asc');

        // 1. Search Logic (Search across all dates if search is active)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {  
                $q->whereHas('user', function($sq) use ($search) {
                    $sq->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                })->orWhere('patient_name', 'like', "%{$search}%");
            });
        }

        // 2. Date Filter
        if ($request->filled('date')) {
            $query->where(function($q) use ($request) {
                $q->whereDate('scheduled_at', $request->date)
                  ->orWhereHas('slot', function($sq) use ($request) {
                      $sq->whereDate('date', $request->date);
                  });
            });
        }

        // 3. Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 4. "Needing Vitals" Quick Filter
        if ($request->has('vitals_only') || $request->has('needing_vitals')) {
            $query->where('status', 'approved')
                ->where(function($q) {
                    $q->whereDoesntHave('healthRecord')
                      ->orWhereHas('healthRecord', function($sq) {
                          $sq->whereNull('vital_signs');
                      });
                });
        }

        // 5. Type Filter (Scheduled vs Walk-in)
        if ($request->filled('type')) {
            if ($request->type === 'walk-in') {
                $query->where('type', 'walk-in');
            } elseif ($request->type === 'scheduled') {
                $query->where(function($q) {
                    $q->where('type', '!=', 'walk-in')
                      ->orWhereNull('type')
                      ->orWhere('type', 'scheduled');
                });
            }
        }

        $appointments = $query->paginate(15)->appends($request->query());
        
        // Counts for tabs/badges
        $counts = [
            'scheduled' => Appointment::whereDate('scheduled_at', $request->date ?? today())
                ->where(fn($q) => $q->where('type', '!=', 'walk-in')->orWhereNull('type'))
                ->count(),
            'walkin' => Appointment::whereDate('scheduled_at', $request->date ?? today())
                ->where('type', 'walk-in')
                ->count(),
            'needing_vitals' => Appointment::whereDate('scheduled_at', $request->date ?? today())
                ->where('status', 'approved')
                ->whereDoesntHave('healthRecord')
                ->count()
        ];

        return view('healthworker.appointments.index', compact('appointments', 'counts'));
    }

    /**
     * Show the form for creating a new appointment
     */
    public function create()
    {
        $patients = Patient::orderBy('last_name')->get();
        
        // Fetch both doctors and midwives
        $doctors = User::whereIn('role', ['doctor', 'midwife'])
            ->where('status', true)
            ->orderBy('last_name')
            ->get();
            
        $services = \App\Models\Service::all();
        
        return view('healthworker.appointments.create', compact('patients', 'doctors', 'services'));
    }

    /**
     * Store a newly created appointment in storage
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'doctor_id' => 'nullable|exists:users,id',
            'service' => 'required|string',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'reason' => 'nullable|string',
        ]);

        // Create a dedicated slot for this walk-in/manual appointment
        $slot = Slot::create([
            'doctor_id' => $request->doctor_id,
            'service' => $request->service,
            'date' => $request->date,
            'start_time' => $request->time,
            'end_time' => Carbon::parse($request->time)->addMinutes(30)->format('H:i'),
            'capacity' => 1,
            'available_spots' => 0, // Immediately taken
            'is_active' => true,
        ]);

        // Create the appointment
        $appointment = Appointment::create([
            'user_id' => $request->patient_id,
            'slot_id' => $slot->id,
            'service' => $request->service,
            'scheduled_at' => Carbon::parse($request->date . ' ' . $request->time),
            'status' => 'approved', // Auto-approve manual appointments
            'type' => 'walk-in', // Tag as walk-in as per requirements
            'notes' => $request->reason,
        ]);

        // Notify patient
        if ($appointment->user) {
            $appointment->user->notify(new AppointmentConfirmed($appointment));
        }

        return redirect()->route('healthworker.appointments.index')
            ->with('success', 'Appointment scheduled successfully.')
            ->with('new_appointment_id', $appointment->id)
            ->with('queue_number', $this->generateQueueNumber($appointment));
    }

    /**
     * Generate a simple queue number based on service and daily sequence.
     */
    private function generateQueueNumber(Appointment $appointment): string
    {
        $serviceInitial = strtoupper(substr($appointment->service, 0, 1));
        $count = Appointment::whereDate('scheduled_at', $appointment->scheduled_at->toDateString())
            ->where('service', $appointment->service)
            ->where('id', '<=', $appointment->id)
            ->count();
            
        return $serviceInitial . '-' . str_pad($count, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Display the specified appointment
     */
    public function show(Appointment $appointment)
    {
        $serviceProvider = null;

        if ($appointment->service) {
            $service = \App\Models\Service::where('name', $appointment->service)->first();
            $serviceProvider = $service?->provider_type;
        }

        return view('healthworker.appointments.show', compact('appointment', 'serviceProvider'));
    }

    /**
     * Show form to edit/reschedule an appointment
     */
    public function edit(Appointment $appointment)
    {
        $services = \App\Models\Service::all();
        return view('healthworker.appointments.edit', compact('appointment', 'services'));
    }

    /**
     * Update appointment (reschedule/edit)
     */
    public function update(Request $request, Appointment $appointment)
    {
        $allowedStatuses = $appointment->type === 'walk-in'
            ? ['pending', 'approved', 'rejected', 'cancelled', 'completed']
            : ['approved', 'cancelled', 'completed'];

        $validator = Validator::make($request->all(), [
            'service' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'status' => 'required|in:' . implode(',', $allowedStatuses),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle Slot Logic
        $originalSlot = $appointment->slot;
        $isRescheduled = false;

        // Check if critical details changed
        $dateChanged = $originalSlot->date->format('Y-m-d') !== $request->date;
        $timeChanged = Carbon::parse($originalSlot->start_time)->format('H:i') !== $request->time;
        
        if ($dateChanged || $timeChanged) {
            $isRescheduled = true;

            if ($appointment->type === 'walk-in') {
                // For walk-ins, we can update the slot directly since it's 1-to-1
                $originalSlot->service = $request->service;
                $originalSlot->date = $request->date;
                $originalSlot->start_time = $request->time;
                $originalSlot->end_time = Carbon::parse($request->time)->addMinutes(30)->format('H:i');
                $originalSlot->save();
            } else {
                // For scheduled appointments, we must NOT modify the shared slot.
                // Instead, we create a new "ad-hoc" slot for the new time (effectively converting to walk-in/custom)
                // OR we could search for an existing slot, but that's complex UI.
                // Let's create a new slot and move the appointment.
                
                // 1. Restore availability to old slot
                $originalSlot->increment('available_spots');

                // 2. Create new slot
                $newSlot = Slot::create([
                    'service' => $request->service,
                    'doctor_id' => $originalSlot->doctor_id, // Keep doctor if possible
                    'date' => $request->date,
                    'start_time' => $request->time,
                    'end_time' => Carbon::parse($request->time)->addMinutes(30)->format('H:i'),
                    'capacity' => 1,
                    'available_spots' => 0, // Immediately taken
                    'is_active' => true,
                ]);

                // 3. Update appointment
                $appointment->slot_id = $newSlot->id;
            }

            $appointment->scheduled_at = Carbon::parse($request->date . ' ' . $request->time);
        }

        // Update other fields
        $appointment->service = $request->service;
        
        // Status change logic
        if ($appointment->status !== $request->status) {
            $appointment->status = $request->status;
            
            // If cancelling, restore slot availability (if we haven't already handled it via reschedule logic)
            if ($request->status === 'cancelled' && !$isRescheduled) {
                 $appointment->slot?->increment('available_spots');
                 if ($appointment->user) {
                     $appointment->user->notify(new AppointmentCancelled($appointment));
                 }
            }
        }

        $appointment->save();

        if ($isRescheduled && $appointment->user) {
            $appointment->user->notify(new AppointmentRescheduled($appointment));
        }

        return redirect()->route('healthworker.appointments.index')
                         ->with('success', 'Appointment updated successfully.');
    }

    /**
     * Approve an appointment
     */
    public function approve(Appointment $appointment)
    {
        if ($appointment->type !== 'walk-in') {
            return redirect()->route('healthworker.appointments.index')
                             ->with('info', 'Appointments booked through patient slots cannot be approved manually.');
        }

        if ($appointment->status === 'pending') {
            $appointment->status = 'approved';
            $appointment->save();

            if ($appointment->user) {
                $appointment->user->notify(new AppointmentConfirmed($appointment));
            }
            return redirect()->route('healthworker.appointments.index')
                             ->with('success', 'Appointment approved successfully.');
        }

        return redirect()->route('healthworker.appointments.index')
                         ->with('info', 'Only pending walk-in appointments can be approved.');
    }

    /**
     * Cancel an appointment
     */
    public function cancel(Appointment $appointment)
    {
        if (!in_array($appointment->status, ['cancelled', 'rejected'])) {
            $appointment->status = 'cancelled';
            $appointment->save();
        }

        return redirect()->route('healthworker.appointments.index')
                         ->with('success', 'Appointment cancelled successfully.');
    }

    /**
     * Show form to add vitals for an appointment
     */
    public function addVitals(Appointment $appointment)
    {
        $serviceProviderLabel = 'Doctor'; // Default

        // 1. Priority: Check the Service model's provider_type
        if ($appointment->service) {
            $service = \App\Models\Service::where('name', $appointment->service)->first();
            if ($service && $service->provider_type && $service->provider_type !== 'Both') {
                $serviceProviderLabel = $service->provider_type;
            }
        }

        // 2. Secondary: If still default or 'Both', check the assigned provider in the slot
        if (($serviceProviderLabel === 'Doctor' || $serviceProviderLabel === 'Provider') && $appointment->slot && $appointment->slot->doctor_id) {
            $provider = $appointment->slot->doctor;
            if ($provider->isMidwife()) {
                $serviceProviderLabel = 'Midwife';
            } elseif ($provider->isDoctor()) {
                $serviceProviderLabel = 'Doctor';
            }
        }

        return view('healthworker.appointments.add-vitals', compact('appointment', 'serviceProviderLabel'));
    }

    /**
     * Store vitals for an appointment
     */
    public function storeVitals(Request $request, Appointment $appointment)
    {
        $rules = [
            'blood_pressure' => 'required|string',
            'temperature' => 'required|numeric',
            'weight' => 'required|numeric',
            'height_cm' => 'required|numeric|min:30|max:250',
            'pulse_rate' => 'required|numeric',
            'respiratory_rate' => 'nullable|numeric',
            'oxygen_saturation' => 'nullable|numeric',
            'metadata' => 'nullable|array',
        ];

        // 🤰 Dynamic validation for Prenatal Care
        $isPrenatal = str_contains(strtolower($appointment->service), 'prenatal') || str_contains(strtolower($appointment->service), 'pre-natal');
        if ($isPrenatal) {
            $rules['metadata.lmp'] = 'required|date';
            $rules['metadata.gravida'] = 'required|numeric|min:1';
            $rules['metadata.para'] = 'required|numeric|min:0';
        }

        // 💉 Dynamic validation for Immunization
        $isImmunization = str_contains(strtolower($appointment->service), 'immunization');
        if ($isImmunization) {
            $rules['metadata.general_condition'] = 'required|in:Healthy,With Fever,Sick';
        }

        $request->validate($rules);

        $weight = (float) $request->weight;
        $heightCm = (float) $request->height_cm;
        $bmi = null;

        if ($heightCm > 0) {
            $heightM = $heightCm / 100;
            $bmi = round($weight / ($heightM * $heightM), 1);
        }

        $vitalSigns = [
            'blood_pressure' => $request->blood_pressure,
            'temperature' => $request->temperature,
            'weight' => $request->weight,
            'height_cm' => $request->height_cm,
            'bmi' => $bmi,
            'pulse_rate' => $request->pulse_rate,
            'respiratory_rate' => $request->respiratory_rate,
            'oxygen_saturation' => $request->oxygen_saturation,
        ];

        $healthRecord = \App\Models\HealthRecord::firstOrNew([
            'appointment_id' => $appointment->id,
        ]);

        $existingMetadata = is_array($healthRecord->metadata) ? $healthRecord->metadata : [];
        $incomingMetadata = is_array($request->metadata) ? $request->metadata : [];

        $healthRecord->fill([
            'patient_id' => $appointment->user_id,
            'created_by' => $healthRecord->created_by ?? auth()->id(),
            'service_id' => $appointment->slot->service_id ?? $healthRecord->service_id,
            'vital_signs' => $vitalSigns,
            'metadata' => array_merge($existingMetadata, $incomingMetadata), // keep prior metadata while updating vitals payload
            'status' => $healthRecord->status ?? 'active',
            'verified_at' => null,
            'verified_by' => null,
        ]);

        $healthRecord->save();

        // 🤰 Sync Prenatal Metadata to Patient Profile if applicable
        if ($request->has('metadata.lmp')) {
            $profile = $appointment->user->patientProfile;
            if ($profile) {
                $profile->update([
                    'lmp' => $request->input('metadata.lmp'),
                    'edd' => $request->input('metadata.edd'),
                    'gravida' => $request->input('metadata.gravida'),
                    'para' => $request->input('metadata.para'),
                    'history_miscarriage' => $request->boolean('metadata.history_miscarriage'),
                    'previous_complications' => $request->input('metadata.previous_complications'),
                ]);
                $profile->evaluateRisk();
            }
        }

        if ($appointment->status === 'pending') {
            $appointment->status = 'approved';
            $appointment->save();

            if ($appointment->user) {
                $appointment->user->notify(new AppointmentConfirmed($appointment));
            }
        }

        return redirect()->route('healthworker.appointments.index')
                         ->with('success', 'Vital signs submitted successfully.');
    }
}
