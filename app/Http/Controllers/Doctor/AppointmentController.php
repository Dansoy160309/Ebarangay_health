<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\HealthRecord;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Inline safeguard: normalize past approved/rescheduled to no_show
        // This complements the scheduled command and ensures UI correctness even if scheduler isn't running.
        $today = now()->startOfDay();
        try {
            Appointment::whereIn('status', ['approved', 'rescheduled'])
                ->where(function ($q) use ($today) {
                    $q->whereHas('slot', function ($sq) use ($today) {
                        $sq->whereDate('date', '<', $today->toDateString());
                    })->orWhere(function ($qq) use ($today) {
                        $qq->whereNull('slot_id')
                           ->whereDate('scheduled_at', '<', $today);
                    });
                })
                ->update(['status' => 'no_show']);
        } catch (\Throwable $e) {
            // Ignore if DB enum not yet migrated; UI fallback will still show correct badge.
        }

        $appointments = Appointment::with(['user', 'slot', 'healthRecord'])
            // Only show appointments assigned to this provider
            ->whereHas('slot', function($q) use ($user) {
                $q->where(function($sq) use ($user) {
                    $sq->where('doctor_id', $user->id);
                    // If midwife, also show slots with no doctor assigned (midwife-led)
                    if ($user->isMidwife()) {
                        $sq->orWhereNull('doctor_id');
                    }
                });
            });

        // Status filter
        // default: show active items (approved + rescheduled)
        $status = $request->get('status');
        if ($status) {
            $statusMap = [
                'active'    => ['approved', 'rescheduled'],
                'completed' => ['completed'],
                'cancelled' => ['cancelled', 'rejected'],
                'no_show'   => ['no_show'],
                'archived'  => ['archived'],
                'pending'   => ['pending'],
                'all'       => ['pending','approved','rescheduled','completed','cancelled','rejected'],
            ];
            $statuses = $statusMap[$status] ?? ['approved', 'rescheduled'];
            // Special handling for "no_show": include past approved/rescheduled even if DB not updated
            if ($status === 'no_show') {
                $appointments->where(function ($q) use ($today) {
                    $q->where('status', 'no_show')
                      ->orWhere(function ($qq) use ($today) {
                          $qq->whereIn('status', ['approved', 'rescheduled'])
                             ->where(function ($aq) use ($today) {
                                 $aq->whereHas('slot', function ($sq) use ($today) {
                                     $sq->whereDate('date', '<', $today->toDateString());
                                 })->orWhere(function ($sq2) use ($today) {
                                     $sq2->whereNull('slot_id')
                                         ->whereDate('scheduled_at', '<', $today);
                                 });
                             });
                      });
                });
            } else {
                $appointments->whereIn('status', $statuses);
            }
        } else {
            $appointments->whereIn('status', ['approved', 'rescheduled']);
        }

        // Hide past appointments from "active" view by default
        // Show only today and future schedules
        if (!$status || $status === 'active') {
            $today = now()->startOfDay();
            $appointments->where(function ($q) use ($today) {
                $q->whereHas('slot', function ($sq) use ($today) {
                    $sq->whereDate('date', '>=', $today->toDateString());
                })->orWhere(function ($qq) use ($today) {
                    $qq->whereNull('slot_id')
                       ->whereDate('scheduled_at', '>=', $today);
                });
            });
        }

        // Date filter (scheduled_at date)
        if ($request->filled('date')) {
            try {
                $date = \Carbon\Carbon::parse($request->date)->toDateString();
                $appointments->where(function ($q) use ($date) {
                    $q->whereHas('slot', function ($sq) use ($date) {
                        $sq->whereDate('date', $date);
                    })->orWhereDate('scheduled_at', $date);
                });
            } catch (\Throwable $e) {
                // ignore invalid date
            }
        }

        // Search by patient
        if ($request->filled('search')) {
            $s = $request->search;
            $appointments->whereHas('user', function($uq) use ($s) {
                $uq->where('first_name', 'like', "%{$s}%")
                   ->orWhere('last_name', 'like', "%{$s}%")
                   ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$s}%"]);
            });
        }

        // Calculate Stats for Header
        $stats = [
            'today_total' => (clone $appointments)->where(function($q) use ($today) {
                $q->whereHas('slot', function($sq) use ($today) {
                    $sq->whereDate('date', $today->toDateString());
                })->orWhereDate('scheduled_at', $today);
            })->count(),
            'ready' => (clone $appointments)->where('status', 'approved')
                ->whereHas('healthRecord', function($q) {
                    $q->whereNotNull('vital_signs');
                })->count(),
            'pending_vitals' => (clone $appointments)->where('status', 'approved')
                ->whereDoesntHave('healthRecord')->count(),
        ];

        $appointments = $appointments
            ->latest('scheduled_at')
            ->paginate(15)
            ->appends($request->query());

        return view('doctor.appointments.index', compact('appointments', 'stats'));
    }

    public function show(Appointment $appointment)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        // Load patient history and current health record
        $appointment->load(['user.healthRecords' => function($q) {
            $q->with('service')->latest()->limit(5);
        }, 'healthRecord', 'user.patientProfile']);

        // Load available vaccine batches if this is an immunization appointment
        $vaccineBatches = [];
        if (str_contains(strtolower($appointment->service), 'immunization')) {
            $vaccineBatches = \App\Models\VaccineBatch::with('vaccine')
                ->where('expiry_date', '>', now())
                ->where('quantity_remaining', '>', 0)
                ->where('is_active', true)
                ->get()
                ->groupBy('vaccine.name');
        }
        
        return view('doctor.appointments.show', compact('appointment', 'vaccineBatches'));
    }

    public function consult(Request $request, Appointment $appointment)
    {
        $request->validate([
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'notes' => 'nullable|string',
            'metadata' => 'nullable|array',
            'profile' => 'nullable|array',
        ]);

        // 🛡️ Clinical Safety Rules for Immunization
        $isImmunization = str_contains(strtolower($appointment->service), 'immunization');
        if ($isImmunization) {
            // 1. Fever Check
            $vitals = $appointment->healthRecord->vital_signs ?? [];
            $temp = (float) ($vitals['temperature'] ?? 0);
            if ($temp > 37.5) {
                return redirect()->back()->withErrors(['error' => 'Safety Block: Vaccination cannot proceed due to patient fever (' . $temp . '°C).'])->withInput();
            }

            // 2. Stock Check
            if ($request->has('metadata.vaccine_batch_id')) {
                $batch = \App\Models\VaccineBatch::find($request->input('metadata.vaccine_batch_id'));
                if (!$batch || $batch->quantity_remaining <= 0) {
                    return redirect()->back()->withErrors(['error' => 'Safety Block: The selected vaccine batch is out of stock.'])->withInput();
                }
                if ($batch->expiry_date->isPast()) {
                    return redirect()->back()->withErrors(['error' => 'Safety Block: The selected vaccine batch has expired.'])->withInput();
                }
            }
        }

        DB::transaction(function () use ($request, $appointment, $isImmunization) {
            // 1. Update or Create Patient Profile if provided (for Prenatal)
            if ($request->has('profile')) {
                $profile = $appointment->user->patientProfile ?? new \App\Models\PatientProfile(['user_id' => $appointment->user_id]);
                
                $profileData = $request->input('profile');
                
                // Handle checkboxes
                $profileData['history_miscarriage'] = $request->has('profile.history_miscarriage');
                $profileData['is_high_risk'] = $request->has('profile.is_high_risk');

                $profile->fill($profileData);
                $profile->save();
                
                // Re-evaluate risk factors (auto-calculates based on age, gravida, etc.)
                $profile->evaluateRisk();
            }

            // 2. Handle Health Record
            $healthRecord = $appointment->healthRecord ?? new HealthRecord();
            
            // Try to find service ID if not set
            $serviceId = $healthRecord->service_id;
            if (!$serviceId && $appointment->service) {
                $service = Service::where('name', $appointment->service)->first();
                $serviceId = $service?->id;
            }

            // 💉 Immunization Specific: Suggest Next Dose
            $metadata = array_merge(
                $healthRecord->metadata ?? [], 
                $request->input('metadata', []),
                ['doctor_notes' => $request->notes]
            );

            if ($isImmunization) {
                $doseNo = (int) ($request->input('metadata.dose_no') ?? 0);
                $nextSchedule = null;
                
                if ($doseNo >= 1 && $doseNo < 3) {
                    $nextSchedule = now()->addWeeks(4)->format('Y-m-d');
                    $metadata['suggested_next_dose_date'] = $nextSchedule;
                    $metadata['schedule_notes'] = 'Suggested 4-week interval per DOH guidelines.';
                }
            }

            $healthRecord->fill([
                'patient_id' => $appointment->user_id,
                'appointment_id' => $appointment->id,
                'service_id' => $serviceId,
                'diagnosis' => $request->diagnosis,
                'treatment' => $request->treatment,
                'consultation' => $request->notes, // Map notes to the consultation field
                'metadata' => $metadata,
                'created_by' => $healthRecord->created_by ?? auth()->id(),
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            if (!$healthRecord->exists && !$healthRecord->vital_signs) {
                $healthRecord->vital_signs = [];
            }

            $healthRecord->save();

            // 3. Decrement Vaccine Stock
            if ($isImmunization && $request->has('metadata.vaccine_batch_id')) {
                $batchId = $request->input('metadata.vaccine_batch_id');
                $qty = (int) ($request->input('metadata.dose_quantity') ?? 1);
                $qty = $qty > 0 ? $qty : 1;

                $batch = \App\Models\VaccineBatch::whereKey($batchId)->lockForUpdate()->first();
                if ($batch && !$batch->expiry_date->isPast() && $batch->is_active) {
                    $alreadyLogged = \App\Models\VaccineAdministration::where('appointment_id', $appointment->id)
                        ->where('vaccine_batch_id', $batch->id)
                        ->exists();

                    if (!$alreadyLogged && $batch->quantity_remaining >= $qty) {
                        $batch->quantity_remaining -= $qty;
                        $batch->quantity_administered += $qty;
                        if ($batch->quantity_remaining <= 0) {
                            $batch->is_active = false;
                        }
                        $batch->save();

                        \App\Models\VaccineAdministration::create([
                            'vaccine_id' => $batch->vaccine_id,
                            'vaccine_batch_id' => $batch->id,
                            'patient_id' => $appointment->user_id,
                            'administered_by' => auth()->id(),
                            'appointment_id' => $appointment->id,
                            'quantity' => $qty,
                            'administered_at' => now(),
                            'notes' => $request->input('metadata.notes'),
                        ]);
                    }
                }
            }

            // 4. Update Appointment Status
            $appointment->update(['status' => 'completed']);
        });

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $routePrefix = $user->isMidwife() ? 'midwife' : 'doctor';
        return redirect()->route($routePrefix . '.appointments.index')->with('success', 'Consultation completed successfully.');
    }
}
