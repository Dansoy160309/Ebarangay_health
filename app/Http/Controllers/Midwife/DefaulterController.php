<?php

namespace App\Http\Controllers\Midwife;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use Carbon\Carbon;
use App\Notifications\DefaulterRecallNotification;

class DefaulterController extends Controller
{
    /**
     * Display a list of "Defaulters" (patients who missed scheduled doses)
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['user.guardian', 'slot'])
            ->whereIn('status', ['approved', 'rescheduled', 'pending'])
            ->where(function($q) {
                $q->whereDate('scheduled_at', '<', Carbon::today())
                  ->orWhereHas('slot', function($sq) {
                      $sq->whereDate('date', '<', Carbon::today());
                  });
            });

        // Search Logic
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($sq) use ($search) {
                $sq->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        // Filter by service if provided
        $serviceFilter = $request->get('service', 'all');
        if ($serviceFilter !== 'all') {
            $query->where('service', 'like', "%{$serviceFilter}%");
        } else {
            // Default: Only critical services (Immunization & Prenatal)
            $query->where(function($q) {
                $q->where('service', 'like', '%Immunization%')
                  ->orWhere('service', 'like', '%Prenatal%');
            });
        }

        $allDefaulters = (clone $query)->get();
        $stats = [
            'total' => $allDefaulters->count(),
            'immunization' => $allDefaulters->filter(fn($a) => str_contains(strtolower($a->service), 'immunization'))->count(),
            'prenatal' => $allDefaulters->filter(fn($a) => str_contains(strtolower($a->service), 'prenatal'))->count(),
        ];

        $defaulters = $query->orderBy('scheduled_at', 'desc')->paginate(15)->appends($request->query());

        return view('midwife.appointments.defaulters', compact('defaulters', 'stats', 'serviceFilter'));
    }

    /**
     * Mark an appointment as "No Show"
     */
    public function markAsNoShow(Appointment $appointment)
    {
        $appointment->update(['status' => 'no_show']);
        return redirect()->back()->with('success', 'Appointment marked as No Show.');
    }

    /**
     * Send a recall SMS to a defaulter
     */
    public function sendRecallSms(Appointment $appointment)
    {
        $patient = $appointment->user;
        
        // Ensure the patient (or guardian) has a contact number
        $recipient = $patient->contact_no;
        if (empty($recipient) && $patient->isDependent()) {
            $recipient = $patient->guardian->contact_no ?? null;
        }

        if (empty($recipient)) {
            return redirect()->back()->with('error', 'Patient or guardian has no contact number registered.');
        }

        // Send the notification
        $patient->notify(new DefaulterRecallNotification($appointment));

        return redirect()->back()->with('success', "Recall alert successfully sent to {$patient->full_name}.");
    }
}
