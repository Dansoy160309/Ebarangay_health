<?php

namespace App\Http\Controllers\Midwife;

use App\Http\Controllers\Controller;
use App\Models\DoctorAvailability;
use App\Models\Appointment;
use App\Notifications\DoctorArrivalNotification;
use App\Notifications\DoctorAbsenceNotification;
use App\Notifications\DoctorDelayedNotification;
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

        $today = now()->toDateString();
        $dayOfWeek = now()->dayOfWeek;

        // Get doctors scheduled for today (both one-time and recurring)
        $availabilities = DoctorAvailability::with('doctor')
            ->where(function($q) use ($today, $dayOfWeek) {
                $q->where('date', $today)
                  ->orWhere(function($sq) use ($dayOfWeek) {
                      $sq->where('is_recurring', true)
                         ->where('recurring_day', $dayOfWeek);
                  });
            })
            ->orderBy('start_time', 'asc')
            ->get();

        // If a doctor has both a one-time and a recurring entry for today,
        // we prioritize the one-time entry (the override).
        $todayAvailabilities = $availabilities->groupBy('doctor_id')->map(function ($group) use ($today) {
            $oneTime = $group->first(fn($a) => !$a->is_recurring && $a->date->toDateString() === $today);
            return $oneTime ?? $group->first();
        })->values();

        return view('midwife.doctor_presence.index', compact('todayAvailabilities'));
    }

    public function markArrived(DoctorAvailability $availability)
    {
        // If this is a recurring availability, create a one-time override for today
        if ($availability->is_recurring) {
            $availability = DoctorAvailability::updateOrCreate(
                [
                    'doctor_id' => $availability->doctor_id,
                    'date' => now()->toDateString(),
                    'is_recurring' => false
                ],
                [
                    'start_time' => $availability->start_time,
                    'end_time' => $availability->end_time,
                    'status' => 'arrived',
                    'actual_arrival_time' => now(),
                    'notes' => $availability->notes
                ]
            );
        } else {
            $availability->update([
                'status' => 'arrived',
                'actual_arrival_time' => now(),
            ]);
        }

        $availability->loadMissing('doctor');
        $notifiedCount = $this->notifyAllPatients($availability, new DoctorArrivalNotification($availability));
        $doctorName = $availability->doctor?->full_name ?? 'Doctor';

        return back()->with('success', "Dr. {$doctorName} marked as arrived. {$notifiedCount} patient(s) notified.");
    }

    public function markAbsent(DoctorAvailability $availability)
    {
        // If this is a recurring availability, create a one-time override for today
        if ($availability->is_recurring) {
            $availability = DoctorAvailability::updateOrCreate(
                [
                    'doctor_id' => $availability->doctor_id,
                    'date' => now()->toDateString(),
                    'is_recurring' => false
                ],
                [
                    'start_time' => $availability->start_time,
                    'end_time' => $availability->end_time,
                    'status' => 'absent',
                    'notes' => 'Doctor is absent today.'
                ]
            );
        } else {
            $availability->update(['status' => 'absent']);
        }

        $availability->loadMissing('doctor');
        $notifiedCount = $this->notifyAllPatients($availability, new DoctorAbsenceNotification($availability));
        $doctorName = $availability->doctor?->full_name ?? 'Doctor';

        return back()->with('warning', "Dr. {$doctorName} marked as absent. {$notifiedCount} patient(s) notified.");
    }

    public function markDelayed(DoctorAvailability $availability, Request $request)
    {
        // If this is a recurring availability, create a one-time override for today
        if ($availability->is_recurring) {
            $availability = DoctorAvailability::updateOrCreate(
                [
                    'doctor_id' => $availability->doctor_id,
                    'date' => now()->toDateString(),
                    'is_recurring' => false
                ],
                [
                    'start_time' => $availability->start_time,
                    'end_time' => $availability->end_time,
                    'status' => 'delayed',
                    'notes' => $request->notes ?? 'Doctor is running late.'
                ]
            );
        } else {
            $availability->update([
                'status' => 'delayed',
                'notes' => $request->notes ?? 'Doctor is running late.'
            ]);
        }

        $availability->loadMissing('doctor');
        $notifiedCount = $this->notifyAllPatients($availability, new DoctorDelayedNotification($availability));
        $doctorName = $availability->doctor?->full_name ?? 'Doctor';

        return back()->with('info', "Dr. {$doctorName} marked as delayed. {$notifiedCount} patient(s) notified.");
    }

    protected function notifyAllPatients(DoctorAvailability $availability, $notification): int
    {
        // Get ALL patients in the system
        $allPatients = \App\Models\User::where('role', 'patient')
            ->where('status', true) // Only active patients
            ->get();

        if ($allPatients->isEmpty()) {
            return 0;
        }

        Notification::send($allPatients, $notification);
        return $allPatients->count();
    }
}
