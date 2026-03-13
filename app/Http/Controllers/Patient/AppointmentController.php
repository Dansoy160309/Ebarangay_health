<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Slot;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Notifications\AppointmentBookedNotification;
use App\Notifications\AppointmentRescheduled;
use App\Notifications\AppointmentCancelled;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:patient']);
    }

    /**
     * Display patient's appointments and available slots.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // One-time fix: Revert future no_show appointments back to approved
        try {
            $now = now();
            Appointment::where('user_id', $user->id)
                ->where('status', 'no_show')
                ->where(function ($q) use ($now) {
                    $q->whereHas('slot', function ($sq) use ($now) {
                        $sq->whereDate('date', '>', $now->toDateString()) // Future date
                           ->orWhere(function($qq) use ($now) { // Today's date, but end_time is in the future
                               $qq->whereDate('date', $now->toDateString())
                                  ->whereTime('end_time', '>', $now->toTimeString());
                           });
                    })->orWhere(function ($qq) use ($now) { // Appointments without slots (direct scheduled_at)
                        $qq->whereNull('slot_id')
                           ->where('scheduled_at', '>', $now);
                    });
                })
                ->update(['status' => 'approved']);
        } catch (\Exception $e) {}

        // Load appointments with slot relationship
        $appointments = Appointment::with(['slot.doctor', 'user', 'healthRecord'])
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('booked_by', $user->id); // Include appointments booked for dependents
            })
            ->latest()
            ->paginate(10);

        // Available slots (only active, future, and not already booked by this user)
        $services = Service::all()->keyBy('name');

        $availableSlots = Slot::with('doctor')
            ->active()
            ->whereDate('date', '>=', now())
            ->where('service', '!=', 'General Checkup') // Exclude General Checkup
            ->withCount(['appointments' => function ($query) {
                $query->whereNotIn('status', ['cancelled', 'rejected']);
            }])
            ->whereDoesntHave('appointments', function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->whereNotIn('status', ['cancelled', 'rejected']);
            })
            ->orderBy('date', 'asc')
            ->get()
            ->filter(function ($slot) use ($services) {
                // Check if full (Capacity - Total Booked) OR explicitly 0 spots OR expired
                if (($slot->capacity - $slot->appointments_count) <= 0 || $slot->available_spots <= 0 || $slot->isExpired()) {
                    return false;
                }
                
                // Check provider requirement
                $service = $services[$slot->service] ?? null;
                if ($service && $service->provider_type === 'Doctor' && !$slot->doctor_id) {
                    return false;
                }

                return true;
            })
            ->values();

        $dependents = $user->dependents()->get();

        return view('patient.appointments.index', compact('appointments', 'availableSlots', 'dependents'));
    }

    /**
     * Display all available slots.
     */
    public function availableSlots(Request $request)
    {
        $user = Auth::user();
        $services = Service::all()->keyBy('name');

        $availableSlots = Slot::with('doctor')
            ->active()
            ->whereDate('date', '>=', now())
            ->where('service', '!=', 'General Checkup') // Exclude General Checkup
            ->withCount(['appointments' => function ($query) {
                $query->whereNotIn('status', ['cancelled', 'rejected']);
            }])
            ->whereDoesntHave('appointments', function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->whereNotIn('status', ['cancelled', 'rejected']);
            })
            ->orderBy('date', 'asc')
            ->get()
            ->filter(function ($slot) use ($services) {
                // Check if full (Capacity - Total Booked) OR explicitly 0 spots OR expired
                if (($slot->capacity - $slot->appointments_count) <= 0 || $slot->available_spots <= 0 || $slot->isExpired()) {
                    return false;
                }
                
                // Check provider requirement
                $service = $services[$slot->service] ?? null;
                if ($service && $service->provider_type === 'Doctor' && !$slot->doctor_id) {
                    return false;
                }

                return true;
            })
            ->values();

        return view('patient.appointments.available-slots', compact('availableSlots'));
    }

    /**
     * Book a slot.
     */
    public function book(Request $request, Slot $slot)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'patient_id' => 'required|exists:users,id',
        ]);

        $patient_id = $request->patient_id;
        
        // Ensure the patient is either the user themselves or one of their dependents
        if ($patient_id != $user->id && !$user->dependents()->where('users.id', $patient_id)->exists()) {
            return redirect()->back()->with('error', 'Invalid patient selected.');
        }

        $patient = \App\Models\User::find($patient_id);

        // Check for existing pending/approved appointment for this service
        $existing = Appointment::where('user_id', $patient_id)
            ->where('service', $slot->service)
            ->whereIn('status', ['pending', 'approved', 'rescheduled'])
            ->first();

        if ($existing) {
            $patientName = ($patient_id == $user->id) ? 'you' : $patient->first_name;
            return redirect()->back()->with('error', "{$patientName} already has an active appointment for {$slot->service}. Please check your upcoming appointments.");
        }

        // Slot Capacity Check
        if (!$slot->isBookable()) {
            return redirect()->back()->with('error', 'This slot is already full.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($user, $patient, $slot) {
            $scheduledAt = $slot->start_time 
                ? Carbon::parse($slot->date)->setTimeFromTimeString($slot->start_time)
                : null;

            $appointment = Appointment::create([
                'user_id'      => $patient->id,
                'booked_by'    => $user->id,
                'slot_id'      => $slot->id,
                'service'      => $slot->service,
                'type'         => 'scheduled',
                'scheduled_at' => $scheduledAt,
                'status'       => 'approved',
                'patient_name' => $patient->full_name,
                'patient_email'=> $patient->email,
            ]);

            $user->notify(new AppointmentBookedNotification($appointment));
            if ($patient->id !== $user->id) {
                $patient->notify(new AppointmentBookedNotification($appointment));
            }

            $slot->decrement('available_spots');
        });

        return redirect()->route('patient.appointments.index')
            ->with('success', 'Appointment booked and confirmed successfully.');
    }

    /**
     * Show appointment details.
     */
    public function show(Appointment $appointment)
    {
        abort_unless($appointment->user_id === Auth::id(), 403);

        // Ensure user and slot relationship is loaded
        $appointment->load(['user', 'slot']);

        return view('patient.appointments.show', compact('appointment'));
    }

    /**
     * Show reschedule form.
     */
    public function edit(Appointment $appointment)
    {
        abort_unless($appointment->user_id === Auth::id(), 403);

        // Load available slots for rescheduling (Same service only)
        $services = Service::all()->keyBy('name');
        
        $availableSlots = Slot::active()
            ->whereDate('date', '>=', now())
            ->where('service', $appointment->service) // Restrict to same service
            ->withCount(['appointments as booked_count' => fn($q) =>
                $q->where('user_id', $appointment->user_id)
                  ->whereNotIn('status', ['cancelled', 'rejected'])
            ])
            ->get()
            ->filter(function ($slot) use ($services) {
                if (!$slot->isBookable() || $slot->booked_count > 0) {
                    return false;
                }

                // Check provider requirement
                $service = $services[$slot->service] ?? null;
                if ($service && $service->provider_type === 'Doctor' && !$slot->doctor_id) {
                    return false;
                }

                return true;
            });

        return view('patient.appointments.edit', compact('appointment', 'availableSlots'));
    }

    /**
     * Reschedule an appointment.
     */
    public function update(Request $request, Appointment $appointment)
    {
        abort_unless($appointment->user_id === Auth::id(), 403);

        $request->validate([
            'slot_id' => 'required|exists:slots,id',
        ]);

        $newSlot = Slot::findOrFail($request->slot_id);

        if (!$newSlot->isBookable()) {
            return back()->with('error', 'The selected slot is unavailable or full.');
        }

        // Restore old slot availability if applicable
        $appointment->slot?->increment('available_spots');

        $scheduledAt = $newSlot->start_time 
            ? Carbon::parse($newSlot->date)->setTimeFromTimeString($newSlot->start_time)
            : null;

        $appointment->update([
            'slot_id'      => $newSlot->id,
            'service'      => $newSlot->service,
            'status'       => 'rescheduled',
            'scheduled_at' => $scheduledAt,
        ]);

        $appointment->user->notify(new AppointmentRescheduled($appointment));

        $newSlot->decrement('available_spots');

        return redirect()->route('patient.appointments.index')
            ->with('success', 'Appointment rescheduled successfully.');
    }

    /**
     * Cancel an appointment.
     */
    public function cancel(Appointment $appointment)
    {
        abort_unless($appointment->user_id === Auth::id(), 403);

        if (in_array($appointment->status, ['cancelled', 'rejected', 'completed'])) {
            return back()->with('error', 'This appointment cannot be cancelled.');
        }

        $appointment->update(['status' => 'cancelled']);
        
        $appointment->user->notify(new AppointmentCancelled($appointment));

        // Restore slot availability
        if ($appointment->slot_id) {
            Slot::where('id', $appointment->slot_id)->increment('available_spots');
        }

        return redirect()->route('patient.appointments.index')
            ->with('success', 'Your appointment has been cancelled successfully.');
    }
}
