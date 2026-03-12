<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use Carbon\Carbon;
use App\Notifications\AppointmentConfirmed;
use App\Notifications\AppointmentCancelled;

class AppointmentController extends Controller
{
    /**
     * Display a listing of all appointments for admin (with optional search/filter).
     */
    public function index(Request $request)
    {
        $appointments = Appointment::with(['user', 'slot'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->date, function ($query, $date) {
                return $query->where(function ($q) use ($date) {
                    $q->whereHas('slot', function ($sq) use ($date) {
                        $sq->whereDate('date', $date);
                    })->orWhereDate('scheduled_at', $date);
                });
            })
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhere('patient_name', 'like', "%{$search}%")
                    ->orWhere('patient_email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        /** @var \Illuminate\Pagination\LengthAwarePaginator $appointments */
        // Map display fields
        $appointments->getCollection()->transform(function ($appointment) {
            $appointment->display_patient_name = $appointment->user 
                ? $appointment->user->full_name 
                : $appointment->patient_name ?? 'Unknown';
            $appointment->display_patient_email = $appointment->user 
                ? $appointment->user->email 
                : $appointment->patient_email ?? 'Unknown';
            $appointment->formatted_date = $appointment->slot?->date 
                ? Carbon::parse($appointment->slot->date)->format('M d, Y') 
                : ($appointment->scheduled_at ? $appointment->scheduled_at->format('M d, Y') : 'Unknown');
            $appointment->formatted_time = $appointment->slot?->start_time 
                ? Carbon::parse($appointment->slot->start_time)->format('g:i A') . 
                  ($appointment->slot->end_time ? ' - ' . Carbon::parse($appointment->slot->end_time)->format('g:i A') : '')
                : ($appointment->scheduled_at ? $appointment->scheduled_at->format('g:i A') : 'Unknown');

            return $appointment;
        });

        return view('admin.appointments.index', compact('appointments'));
    }

    /**
     * Show details of a single appointment.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['user', 'slot']);
        // Map display fields
        $appointment->display_patient_name = $appointment->user 
            ? $appointment->user->full_name 
            : $appointment->patient_name ?? 'Unknown';
        $appointment->display_patient_email = $appointment->user 
            ? $appointment->user->email 
            : $appointment->patient_email ?? 'Unknown';
        return view('admin.appointments.show', compact('appointment'));
    }

    /**
     * Return upcoming appointments for the admin dashboard.
     */
    public function upcomingAppointments()
    {
        $upcomingAppointments = Appointment::with(['user', 'slot'])
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at', 'asc')
            ->get()
            ->map(function($appointment) {
                $appointment->display_patient_name = $appointment->user 
                    ? $appointment->user->full_name 
                    : $appointment->patient_name ?? 'Unknown';
                $appointment->display_patient_email = $appointment->user 
                    ? $appointment->user->email 
                    : $appointment->patient_email ?? 'Unknown';
                $appointment->formatted_date_time = $appointment->scheduled_at 
                    ? $appointment->scheduled_at->format('M d, Y h:i A') 
                    : 'Unknown';
                return $appointment;
            });

        return $upcomingAppointments;
    }

    /**
     * Approve the given appointment if pending.
     */
    public function approve(Appointment $appointment)
    {
        if ($appointment->type !== 'walk-in') {
            return back()->with('info', 'Appointments booked through patient slots cannot be approved manually.');
        }

        if ($appointment->status === 'pending') {
            $appointment->update(['status' => 'approved']);
            
            if ($appointment->user) {
                $appointment->user->notify(new AppointmentConfirmed($appointment));
            }

            return back()->with('success', 'Appointment approved successfully.');
            return back()->with('success', 'Appointment approved successfully.');
        }

        return back()->with('info', 'Only pending walk-in appointments can be approved.');
    }

    /**
     * Reject the given appointment if pending.
     */
    public function reject(Appointment $appointment)
    {
        if ($appointment->type !== 'walk-in') {
            return back()->with('info', 'Appointments booked through patient slots cannot be rejected manually.');
        }

        if ($appointment->status === 'pending') {
            $appointment->update(['status' => 'rejected']);
            
            if ($appointment->user) {
                $appointment->user->notify(new AppointmentCancelled($appointment));
            }

            return back()->with('error', 'Appointment rejected.');
            return back()->with('error', 'Appointment rejected.');
        }

        return back()->with('info', 'Only pending walk-in appointments can be rejected.');
    }

    /**
     * Cancel an appointment (if not already cancelled/rejected).
     */
    public function cancel(Appointment $appointment)
    {
        if (!in_array($appointment->status, ['cancelled', 'rejected'])) {
            $appointment->update(['status' => 'cancelled']);
            
            if ($appointment->user) {
                $appointment->user->notify(new AppointmentCancelled($appointment));
            }

            return back()->with('success', 'Appointment cancelled successfully.');
        }

        return back()->with('info', 'Cannot cancel an appointment that is already cancelled or rejected.');
    }

    /**
     * Reschedule the appointment (stub for future implementation).
     */
    public function reschedule(Request $request, Appointment $appointment)
    {
        return back()->with('info', 'Reschedule functionality is not implemented yet.');
    }

    /**
     * Show the form for creating a new appointment.
     */
    public function create()
    {
        $patients = \App\Models\Patient::orderBy('last_name')->get();
        $doctors = \App\Models\Doctor::orderBy('last_name')->get();
        $services = \App\Models\Service::all();
        
        return view('admin.appointments.create', compact('patients', 'doctors', 'services'));
    }

    /**
     * Store a newly created appointment in storage.
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
        $slot = \App\Models\Slot::create([
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

        return redirect()->route('admin.appointments.index')
            ->with('success', 'Appointment scheduled successfully.');
    }

    /**
     * Show form to edit/reschedule an appointment.
     */
    public function edit(Appointment $appointment)
    {
        $services = \App\Models\Service::all();
        $doctors = \App\Models\Doctor::orderBy('last_name')->get();
        return view('admin.appointments.edit', compact('appointment', 'services', 'doctors'));
    }

    /**
     * Update appointment (reschedule/edit).
     */
    public function update(Request $request, Appointment $appointment)
    {
        $allowedStatuses = $appointment->type === 'walk-in'
            ? ['pending', 'approved', 'rejected', 'cancelled', 'completed']
            : ['approved', 'cancelled', 'completed'];

        $request->validate([
            'service' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'status' => 'required|in:' . implode(',', $allowedStatuses),
            'doctor_id' => 'nullable|exists:users,id',
        ]);

        // Handle Slot Logic
        $originalSlot = $appointment->slot;
        $isRescheduled = false;

        // Check if critical details changed
        $dateChanged = $originalSlot->date->format('Y-m-d') !== $request->date;
        $timeChanged = Carbon::parse($originalSlot->start_time)->format('H:i') !== $request->time;
        $doctorChanged = $originalSlot->doctor_id != $request->doctor_id;
        
        if ($dateChanged || $timeChanged || $doctorChanged) {
            $isRescheduled = true;

            if ($appointment->type === 'walk-in') {
                // For walk-ins, we can update the slot directly since it's 1-to-1
                $originalSlot->service = $request->service;
                $originalSlot->date = $request->date;
                $originalSlot->start_time = $request->time;
                $originalSlot->doctor_id = $request->doctor_id;
                $originalSlot->end_time = Carbon::parse($request->time)->addMinutes(30)->format('H:i');
                $originalSlot->save();
            } else {
                // For scheduled appointments, we must NOT modify the shared slot.
                // Instead, we create a new "ad-hoc" slot for the new time (effectively converting to walk-in/custom)
                
                // 1. Restore availability to old slot
                $originalSlot->increment('available_spots');

                // 2. Create new slot
                $newSlot = \App\Models\Slot::create([
                    'service' => $request->service,
                    'doctor_id' => $request->doctor_id ?? $originalSlot->doctor_id,
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
            
            // If cancelling, restore slot availability
            if ($request->status === 'cancelled' && !$isRescheduled) {
                 $appointment->slot?->increment('available_spots');
                 if ($appointment->user) {
                     $appointment->user->notify(new AppointmentCancelled($appointment));
                 }
            }
        }

        $appointment->save();

        if ($isRescheduled && $appointment->user) {
            $appointment->user->notify(new \App\Notifications\AppointmentRescheduled($appointment));
        }

        return redirect()->route('admin.appointments.index')
                         ->with('success', 'Appointment updated successfully.');
    }

    /**
     * Remove the specified appointment from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return back()->with('success', 'Appointment deleted successfully.');
    }
}
