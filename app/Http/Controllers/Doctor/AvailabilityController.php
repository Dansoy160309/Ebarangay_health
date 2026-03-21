<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\DoctorAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
    public function index()
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('doctor_availabilities')) {
            $availabilities = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            return view('doctor.availability.index', compact('availabilities'))
                ->with('error', 'The system is currently undergoing a database update. Please contact the administrator to run migrations.');
        }

        $availabilities = DoctorAvailability::where('doctor_id', Auth::id())
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'asc')
            ->paginate(15);

        return view('doctor.availability.index', compact('availabilities'));
    }

    public function create()
    {
        return view('doctor.availability.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'notes' => 'nullable|string|max:500',
            'is_recurring' => 'boolean',
            'recurring_day' => 'required_if:is_recurring,1|nullable|integer|between:0,6',
        ]);

        DoctorAvailability::create([
            'doctor_id' => Auth::id(),
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'notes' => $request->notes,
            'is_recurring' => $request->boolean('is_recurring'),
            'recurring_day' => $request->recurring_day,
            'status' => 'scheduled',
        ]);

        return redirect()->route('doctor.availability.index')
            ->with('success', 'Duty schedule block added successfully.');
    }

    public function destroy(DoctorAvailability $availability)
    {
        if ($availability->doctor_id !== Auth::id()) {
            abort(403);
        }

        $availability->delete();

        return redirect()->route('doctor.availability.index')
            ->with('success', 'Schedule block removed.');
    }
}
