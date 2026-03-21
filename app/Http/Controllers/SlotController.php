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
        return view('slots.create', [
            'services' => $this->getServices(),
            'doctors' => $doctors
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
            'date' => 'required|date|after_or_equal:today',
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

            // Check Doctor Availability
            $isAvailable = \App\Models\DoctorAvailability::where('doctor_id', $request->doctor_id)
                ->where('date', $request->date)
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

        if ($service->provider_type === 'Midwife' && !empty($request->doctor_id)) {
            // Check if the assigned user is actually a midwife (allowed) or a doctor (blocked)
            $assignedUser = User::find($request->doctor_id);
            if ($assignedUser && $assignedUser->role === 'doctor') {
                throw ValidationException::withMessages([
                    'doctor_id' => "The service '{$service->name}' can only be provided by a Midwife. You cannot assign a Doctor to this slot."
                ]);
            }
        }

        // Default checkbox value
        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : true;

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
        return view('slots.edit', [
            'slot' => $slot,
            'services' => $this->getServices(),
            'doctors' => $doctors
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
        if ($service->provider_type === 'Doctor' && empty($request->doctor_id)) {
            throw ValidationException::withMessages([
                'doctor_id' => "The service '{$service->name}' requires an assigned Doctor."
            ]);
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
