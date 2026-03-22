<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slot;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

use App\Models\User;

class SlotController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * List all slots with filters and service dropdown.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Automatically deactivate past slots for managers (admin or midwife)
        if (in_array($user->role, ['admin', 'midwife'], true)) {
            Slot::whereDate('date', '<', today())->update(['is_active' => false]);
        }

        $query = Slot::with('doctor'); // Eager load doctor

        // Filter by service
        if ($request->filled('service')) {
            $query->where('service', 'like', "%{$request->service}%");
        }

        // Filter by date
        if ($request->filled('date')) {
            try {
                $date = Carbon::parse($request->date)->format('Y-m-d');
                $query->whereDate('date', $date);
            } catch (\Exception $e) {
                // ignore invalid date
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $query->orderBy('date')->orderBy('start_time');

        // Header Stats
        $stats = [
            'today_slots' => Slot::whereDate('date', today())->count(),
            'active_slots' => Slot::where('is_active', true)->whereDate('date', '>=', today())->count(),
            'total_capacity' => Slot::where('is_active', true)->whereDate('date', '>=', today())->sum('capacity'),
        ];

        $slots = $query->withCount('appointments')->paginate(15);

        // Get standard services for dropdown
        $services = $this->getServices();

        // Return view based on role
        if ($user->role === 'midwife') {
            return view('slots.index', compact('slots', 'services', 'stats'));
        }

        if ($user->role === 'health_worker') {
            return view('healthworker.slots.index', compact('slots', 'services', 'stats'));
        }

        abort(403);
    }

    /**
     * Show create slot form.
     */
    public function create()
    {
        $this->authorizeMidwife();
        $doctors = User::whereIn('role', ['doctor', 'midwife'])->get();
        $availabilities = \App\Models\DoctorAvailability::whereIn('status', ['scheduled', 'arrived', 'delayed'])
            ->get();

        return view('slots.create', [
            'services' => $this->getServices(),
            'doctors' => $doctors,
            'availabilities' => $availabilities
        ]);
    }

    /**
     * Store a new slot.
     */
    public function store(Request $request)
    {
        $this->authorizeMidwife();

        $validated = $request->validate([
            'service' => 'required|string|max:255|exists:services,name',
            'doctor_id' => 'nullable|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'capacity' => 'required|integer|min:1',
            'is_active' => 'sometimes|boolean',
        ]);

        // Enforce Provider Type Logic
        $service = Service::where('name', $request->service)->first();
        
        if ($service->provider_type === 'Doctor') {
            if (empty($request->doctor_id)) {
                throw ValidationException::withMessages([
                    'doctor_id' => "The service '{$service->name}' requires an assigned Doctor."
                ]);
            }

            // Check Doctor Availability (including recurring)
            $dayOfWeek = Carbon::parse($request->date)->dayOfWeek; // 0-6

            $isAvailable = \App\Models\DoctorAvailability::where('doctor_id', $request->doctor_id)
                ->where(function($q) use ($request, $dayOfWeek) {
                    $q->where(function($sq) use ($request) {
                        $sq->where('is_recurring', false)
                           ->where('date', $request->date);
                    })->orWhere(function($sq) use ($dayOfWeek) {
                        $sq->where('is_recurring', true)
                           ->where('recurring_day', $dayOfWeek);
                    });
                })
                ->where('start_time', '<=', $request->start_time)
                ->where('end_time', '>=', ($request->end_time ?? $request->start_time))
                ->exists();

            if (!$isAvailable) {
                $doctor = User::find($request->doctor_id);
                throw ValidationException::withMessages([
                    'doctor_id' => "Dr. {$doctor->full_name} has not set their availability for this date and time window."
                ]);
            }
        }

        // Default checkbox value
        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : false;

        Slot::create($validated);

        return redirect()->route('midwife.slots.index')->with('success', 'Slot created successfully.');
    }

    /**
     * Show edit slot form.
     */
    public function edit(Slot $slot)
    {
        $this->authorizeMidwife();
        $doctors = User::whereIn('role', ['doctor', 'midwife'])->get();
        $availabilities = \App\Models\DoctorAvailability::where('date', '>=', today())
            ->whereIn('status', ['scheduled', 'arrived', 'delayed'])
            ->get();

        return view('slots.edit', [
            'slot' => $slot,
            'services' => $this->getServices(),
            'doctors' => $doctors,
            'availabilities' => $availabilities
        ]);
    }

    /**
     * Update slot.
     */
    public function update(Request $request, Slot $slot)
    {
        $this->authorizeMidwife();

        $validated = $request->validate([
            'service' => 'required|string|max:255|exists:services,name',
            'doctor_id' => 'nullable|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'capacity' => 'required|integer|min:1',
            'is_active' => 'sometimes|boolean',
        ]);

        // Enforce Provider Type Logic
        $service = Service::where('name', $request->service)->first();
        if ($service->provider_type === 'Doctor') {
            if (empty($request->doctor_id)) {
                throw ValidationException::withMessages([
                    'doctor_id' => "The service '{$service->name}' requires an assigned Doctor."
                ]);
            }

            // Check Doctor Availability (including recurring)
            $dayOfWeek = Carbon::parse($request->date)->dayOfWeek;

            $isAvailable = \App\Models\DoctorAvailability::where('doctor_id', $request->doctor_id)
                ->where(function($q) use ($request, $dayOfWeek) {
                    $q->where(function($sq) use ($request) {
                        $sq->where('is_recurring', false)
                           ->where('date', $request->date);
                    })->orWhere(function($sq) use ($dayOfWeek) {
                        $sq->where('is_recurring', true)
                           ->where('recurring_day', $dayOfWeek);
                    });
                })
                ->where('start_time', '<=', $request->start_time)
                ->where('end_time', '>=', ($request->end_time ?? $request->start_time))
                ->exists();

            if (!$isAvailable) {
                $doctor = User::find($request->doctor_id);
                throw ValidationException::withMessages([
                    'doctor_id' => "Dr. {$doctor->full_name} has not set their availability for this date and time window."
                ]);
            }
        }

        // Default checkbox value
        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : false;

        $slot->update($validated);

        return redirect()->route('midwife.slots.index')->with('success', 'Slot updated successfully.');
    }

    /**
     * Delete slot.
     */
    public function destroy(Slot $slot)
    {
        $this->authorizeMidwife();
        $slot->delete();
        return back()->with('success', 'Slot deleted successfully.');
    }

    /**
     * Only admin can perform certain actions.
     */
    private function authorizeMidwife()
    {
        if (Auth::user()->role !== 'midwife') {
            abort(403);
        }
    }

    /**
     * Returns a list of services from the database.
     */
    private function getServices()
    {
        return Service::all();
    }
}
