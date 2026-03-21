<?php

namespace App\Http\Controllers\Midwife;

use App\Http\Controllers\Controller;
use App\Models\DoctorAvailability;
use App\Models\Appointment;
use App\Notifications\DoctorArrivalNotification;
use App\Notifications\DoctorAbsenceNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class DoctorPresenceController extends Controller
{
    public function index()
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('doctor_availabilities')) {
            $todayAvailabilities = collect();
            return view('midwife.doctor_presence.index', compact('todayAvailabilities'))
                ->with('error', 'Database tables are currently being updated. Please run migrations.');
        }

        // Get doctors scheduled for today
        $todayAvailabilities = DoctorAvailability::with('doctor')
            ->where('date', now()->toDateString())
            ->orderBy('start_time', 'asc')
            ->get();

        return view('midwife.doctor_presence.index', compact('todayAvailabilities'));
    }

    public function markArrived(DoctorAvailability $availability)
    {
        $availability->update([
            'status' => 'arrived',
            'actual_arrival_time' => now(),
        ]);

        // Notify patients scheduled for today with this doctor
        $this->notifyPatients($availability, new DoctorArrivalNotification($availability));

        return back()->with('success', "Dr. {$availability->doctor->full_name} marked as arrived. Notifications sent to scheduled patients.");
    }

    public function markAbsent(DoctorAvailability $availability)
    {
        $availability->update(['status' => 'absent']);

        // Notify patients scheduled for today with this doctor
        $this->notifyPatients($availability, new DoctorAbsenceNotification($availability));

        return back()->with('warning', "Dr. {$availability->doctor->full_name} marked as absent. Absence notifications sent to scheduled patients.");
    }

    public function markDelayed(DoctorAvailability $availability, Request $request)
    {
        $availability->update([
            'status' => 'delayed',
            'notes' => $request->notes ?? 'Doctor is running late.'
        ]);

        return back()->with('info', "Dr. {$availability->doctor->full_name} marked as delayed.");
    }

    protected function notifyPatients(DoctorAvailability $availability, $notification)
    {
        // Find all patients with appointments today in slots linked to this doctor
        // We need to check appointments where the slot has this doctor_id and date
        $patients = \App\Models\User::whereHas('appointments', function($q) use ($availability) {
            $q->whereHas('slot', function($sq) use ($availability) {
                $sq->where('doctor_id', $availability->doctor_id)
                   ->where('date', $availability->date->toDateString());
            });
        })->get();

        if ($patients->isNotEmpty()) {
            Notification::send($patients, $notification);
            
            // Logic for SMS sending would go here if PhilSMS is integrated
            // foreach($patients as $patient) { ... }
        }
    }
}
