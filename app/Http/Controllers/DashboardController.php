<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Patient;
use App\Models\Slot;
use App\Models\Medicine;
use App\Models\MedicineDistribution;
use App\Models\Announcement;
use App\Models\PatientProfile;
use App\Models\HealthRecord;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Main dashboard logic
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // =====================================
        // ⚠️ Force password change check
        // =====================================
        if ($user->must_change_password) {
            return redirect()->route('password.change.notice')
                ->with('warning', 'You must change your password before accessing your dashboard.');
        }

        // =====================================
        // 🧩 ADMIN DASHBOARD
        // =====================================
        if ($user->isAdmin()) {
            $totalResidents = Patient::count();

            $pendingAppointments   = Appointment::where('status', 'pending')->count();
            $approvedAppointments  = Appointment::where('status', 'approved')->count();
            $rejectedAppointments  = Appointment::where('status', 'rejected')->count();

            $upcomingAppointments = Appointment::with(['user', 'slot'])
                ->where('scheduled_at', '>=', now())
                ->orderBy('scheduled_at')
                ->limit(5)
                ->get();

            $appointments = Appointment::with(['user', 'slot'])
                ->where('scheduled_at', '>=', now()->subMonths(3))
                ->orderByDesc('scheduled_at')
                ->limit(100)
                ->get();

            // 📊 ANALYTICS DATA
            // Monthly Appointment Trends (Last 6 months)
            $monthlyStats = Appointment::selectRaw('MONTH(scheduled_at) as month, MONTHNAME(scheduled_at) as month_name, COUNT(*) as count')
                ->where('scheduled_at', '>=', now()->subMonths(6))
                ->groupBy('month', 'month_name')
                ->orderBy('month')
                ->get();

            // Service Utilization Stats
            $serviceStats = Appointment::selectRaw('service, COUNT(*) as count')
                ->groupBy('service')
                ->orderByDesc('count')
                ->get();

            // 💊 MEDICINE DATA
            $medicineStats = [
                'total_stock' => Medicine::sum('stock'),
                'low_stock_count' => Medicine::whereRaw('stock <= reorder_level')->count(),
                'recent_distributions' => MedicineDistribution::with(['medicine', 'patient'])
                    ->latest()
                    ->limit(5)
                    ->get(),
                'distribution_trends' => MedicineDistribution::selectRaw('DATE(distributed_at) as date, SUM(quantity) as count')
                    ->where('distributed_at', '>=', now()->subDays(7))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),
                'fast_moving' => MedicineDistribution::selectRaw('medicine_id, SUM(quantity) as total_quantity')
                    ->groupBy('medicine_id')
                    ->orderByDesc('total_quantity')
                    ->with('medicine')
                    ->limit(5)
                    ->get(),
                'expiring_soon' => Medicine::whereNotNull('expiration_date')
                    ->where('expiration_date', '<=', now()->addMonths(3))
                    ->orderBy('expiration_date')
                    ->get()
            ];

            // 🤰 MATERNAL HEALTH STATS
            $maternalStats = [
                'high_risk_count' => PatientProfile::where('is_high_risk', true)
                    ->whereHas('user', function($q) {
                        $q->where('gender', 'Female');
                    })
                    ->count(),
                'upcoming_edd' => PatientProfile::with('user')
                    ->whereNotNull('edd')
                    ->where('edd', '>=', now())
                    ->where('edd', '<=', now()->addDays(30))
                    ->whereHas('user', function($q) {
                        $q->where('gender', 'Female');
                    })
                    ->get(),
                'active_pregnancies' => PatientProfile::whereNotNull('edd')
                    ->where('edd', '>=', now())
                    ->whereHas('user', function($q) {
                        $q->where('gender', 'Female');
                    })
                    ->count(),
            ];

            // 💉 IMMUNIZATION STATS
            $immunizationStats = [
                'fully_immunized' => PatientProfile::where('is_fully_immunized', true)->count(),
                'total_children' => User::where('role', 'patient')
                    ->where('dob', '>=', now()->subYears(5))
                    ->count(),
                'missed_doses' => PatientProfile::where('is_fully_immunized', false)
                    ->whereHas('user', function($q) {
                        $q->where('dob', '>=', now()->subYears(5));
                    })
                    ->count(),
            ];

            // 🧬 MORBIDITY STATS (Proxy from Service Utilization)
            $morbidityStats = Appointment::selectRaw('service as condition_name, COUNT(*) as case_count')
                ->where('status', 'completed')
                ->groupBy('service')
                ->orderByDesc('case_count')
                ->limit(5)
                ->get();

            // 📢 HEALTH ADVISORIES
            $healthAdvisories = Announcement::where('status', 'active')
                ->latest()
                ->limit(3)
                ->get();

            // 🧬 HEALTH ANALYTICS
            $healthAnalytics = [
                'patient_growth' => Patient::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subMonths(6))
                    ->groupBy('month')
                    ->get(),
                'gender_distribution' => Patient::selectRaw('gender, COUNT(*) as count')
                    ->groupBy('gender')
                    ->get(),
                'appointment_analytics' => [
                    'no_show_rate' => Appointment::whereIn('status', ['cancelled', 'rejected'])->count() / max(Appointment::count(), 1) * 100,
                    'peak_days' => Appointment::selectRaw('DAYNAME(scheduled_at) as day, COUNT(*) as count')
                        ->groupBy('day')
                        ->orderByDesc('count')
                        ->get()
                ]
            ];

            $calendarEvents = $appointments->map(function($appt){
                $serviceColors = [
                    'Immunization' => '#3b82f6', // blue-500
                    'Prenatal' => '#a855f7', // purple-500
                    'Dental' => '#14b8a6', // teal-500
                    'Checkup' => '#6366f1', // indigo-500
                    'Consultation' => '#0ea5e9', // sky-500
                    'Emergency' => '#ef4444', // red-500
                ];

                // Determine color: Status takes precedence if not approved, otherwise Service color
                $color = match($appt->status) {
                    'pending' => '#eab308', // yellow-500
                    'rejected' => '#ef4444', // red-500
                    'cancelled' => '#9ca3af', // gray-400
                    'approved' => $serviceColors[$appt->service] ?? '#22c55e', // service color or default green
                    default => '#9ca3af',
                };

                return [
                    'title' => ($appt->service ?? 'Appt') . ' - ' . ($appt->user ? $appt->user->first_name : ($appt->patient_name ?? 'Guest')),
                    'start' => $appt->scheduled_at ? $appt->scheduled_at->toIso8601String() : null,
                    'url' => route('admin.appointments.show', $appt->id),
                    'color' => $color,
                ];
            })->filter(fn($event) => $event['start'] !== null)->values();

            $notifications = $user->notifications()->latest()->limit(10)->get();

            // ✅ Correct view reference
            return view('admin.dashboard', compact(
                'user',
                'totalResidents',
                'pendingAppointments',
                'approvedAppointments',
                'rejectedAppointments',
                'upcomingAppointments',
                'appointments',
                'calendarEvents',
                'notifications',
                'monthlyStats',
                'serviceStats',
                'medicineStats',
                'healthAdvisories',
                'healthAnalytics',
                'maternalStats',
                'immunizationStats',
                'morbidityStats'
            ));
        }

        // =====================================
        // 🩺 DOCTOR / MIDWIFE DASHBOARD
        // =====================================
        if ($user->isDoctor() || $user->isMidwife()) {
            $baseQuery = Appointment::whereHas('slot', function($q) use ($user) {
                $q->where(function($sq) use ($user) {
                    $sq->where('doctor_id', $user->id);
                    if ($user->isMidwife()) {
                        $sq->orWhereNull('doctor_id');
                    }
                });
            });

            // Focus on today's work
            $today = today();

            // Strict Today Filter: Priority to Slot date, fallback to scheduled_at
            $applyTodayFilter = function($query) use ($today) {
                $query->where(function($q) use ($today) {
                    $q->where(function($sq) use ($today) {
                        $sq->whereNotNull('slot_id')
                          ->whereHas('slot', function($ssq) use ($today) {
                              $ssq->whereDate('date', $today);
                          });
                    })->orWhere(function($sq) use ($today) {
                        $sq->whereNull('slot_id')
                          ->whereDate('scheduled_at', $today);
                    });
                });
            };

            $totalAppointments = (clone $baseQuery);
            $applyTodayFilter($totalAppointments);
            $totalAppointments = $totalAppointments->whereIn('status', ['pending', 'approved', 'rescheduled'])
                ->count();
            
            // Focus on Approved appointments that have vital signs (Health Record exists)
            $pendingConsultationsQuery = (clone $baseQuery)
                ->where('status', 'approved');
            $applyTodayFilter($pendingConsultationsQuery);
            $pendingConsultationsQuery->whereHas('healthRecord', function($q) {
                    $q->whereNotNull('vital_signs');
                });

            $pendingConsultations = (clone $pendingConsultationsQuery)->count();
            
            $upcomingToday = (clone $baseQuery);
            $applyTodayFilter($upcomingToday);
            $upcomingToday = $upcomingToday->whereIn('status', ['approved'])
                ->count();

            $totalPatients = Patient::count();

            $recentAppointments = (clone $baseQuery)->with(['user', 'slot', 'healthRecord'])
                ->where('status', 'approved');
            $applyTodayFilter($recentAppointments);
            $recentAppointments = $recentAppointments->latest('scheduled_at')
                ->limit(5)
                ->get();

             // 📋 Today's Patient List (Specific for Doctor/Midwife - Approved with Vitals)
            $todaysAppointmentsList = (clone $baseQuery)->with(['user', 'slot', 'healthRecord'])
                ->where('status', 'approved');
            $applyTodayFilter($todaysAppointmentsList);
            $todaysAppointmentsList = $todaysAppointmentsList->whereHas('healthRecord', function($q) {
                    $q->whereNotNull('vital_signs');
                })
                ->orderBy('scheduled_at')
                ->get();

            // 🤰 MIDWIFE SPECIFIC MONITORING
            $midwifeAlerts = [];
            if ($user->isMidwife()) {
                $midwifeAlerts = [
                    'high_risk' => PatientProfile::with('user')
                        ->where('is_high_risk', true)
                        ->where(function($q) {
                            $q->whereNotNull('edd')->where('edd', '>=', now())
                              ->orWhereNotNull('lmp')->where('lmp', '>=', now()->subWeeks(42));
                        })
                        ->whereHas('user', function($q) {
                            $q->where('gender', 'Female');
                        })
                        ->limit(5)
                        ->get(),
                    'overdue_prenatal' => PatientProfile::with('user')
                        ->whereNotNull('edd')
                        ->where('edd', '>=', now())
                        ->whereHas('user', function($q) {
                            $q->where('gender', 'Female');
                        })
                        ->limit(5)
                        ->get(),
                    'immunization_due' => PatientProfile::with('user')
                        ->where('is_fully_immunized', false)
                        ->whereHas('user', function($q) {
                            $q->where('dob', '>=', now()->subYears(5));
                        })
                        ->limit(5)
                        ->get(),
                ];
            }

             $routePrefix = $user->isMidwife() ? 'midwife' : 'doctor';
             $calendarEvents = $recentAppointments->map(function($appt) use ($routePrefix) {
                return [
                    'title' => $appt->service,
                    'start' => $appt->scheduled_at ? $appt->scheduled_at->toIso8601String() : null,
                    'url'   => route($routePrefix . '.appointments.show', $appt->id),
                    'color' => '#22c55e', // Approved
                ];
            })->filter(fn($event) => $event['start'] !== null)->values();

            $notifications = $user->notifications()->latest()->limit(10)->get();

            return view('doctor.dashboard', compact(
                'user',
                'totalAppointments',
                'pendingConsultations',
                'upcomingToday',
                'totalPatients',
                'recentAppointments',
                'calendarEvents',
                'notifications',
                'todaysAppointmentsList',
                'midwifeAlerts'
            ));
        }

        // =====================================
        // 🩺 HEALTH WORKER DASHBOARD
        // =====================================
        if ($user->isHealthWorker()) {
            $totalAppointments = Appointment::whereIn('status', ['pending', 'approved', 'rescheduled', 'completed'])->count();

            // Needing Vitals: Approved appointments for today that don't have vital signs yet
            $needingVitalsQuery = Appointment::where('status', 'approved')
                ->whereDate('scheduled_at', today())
                ->where(function($q) {
                    $q->whereDoesntHave('healthRecord')
                      ->orWhereHas('healthRecord', function($sq) {
                          $sq->whereNull('vital_signs');
                      });
                });
            
            $needingVitalsCount = (clone $needingVitalsQuery)->count();

            $upcomingToday = Appointment::whereDate('scheduled_at', today())
                ->whereIn('status', ['approved', 'rescheduled'])
                ->count();

            $totalPatients = Patient::count();

            $recentAppointments = Appointment::with(['user', 'slot', 'healthRecord'])
                ->latest('updated_at')
                ->limit(5)
                ->get();

            // 📋 Today's Patient List (Focus on those needing vitals or scheduled today)
            $todaysAppointmentsList = Appointment::with(['user', 'slot', 'healthRecord'])
                ->where(function($q) {
                    $q->whereDate('scheduled_at', today())
                      ->orWhereHas('slot', function($sq) {
                          $sq->whereDate('date', today());
                      });
                })
                ->whereIn('status', ['pending', 'approved', 'rescheduled', 'completed', 'cancelled'])
                ->orderByRaw("CASE 
                    WHEN status = 'approved' THEN 0 
                    WHEN status = 'rescheduled' THEN 1
                    WHEN status = 'pending' THEN 2
                    WHEN status = 'completed' THEN 3
                    WHEN status = 'cancelled' THEN 4
                    ELSE 5 END")
                ->orderBy('scheduled_at')
                ->get();

            // 📊 Service Stats for Health Worker
            $serviceStats = Appointment::selectRaw('service, COUNT(*) as count')
                ->groupBy('service')
                ->orderByDesc('count')
                ->get()
                ->map(function ($stat) use ($totalAppointments) {
                    $stat->percentage = $totalAppointments > 0 ? ($stat->count / $totalAppointments) * 100 : 0;
                    return $stat;
                });

            $calendarEvents = $recentAppointments->map(function($appt){
                return [
                    'title' => $appt->service,
                    'start' => $appt->scheduled_at ? $appt->scheduled_at->toIso8601String() : null,
                    'url'   => route('healthworker.appointments.show', $appt->id),
                    'color' => match($appt->status) {
                        'approved' => '#22c55e',
                        'completed' => '#10b981',
                        'rejected' => '#f87171',
                        'cancelled' => '#6b7280',
                        'rescheduled' => '#3b82f6',
                        default => '#6b7280',
                    },
                ];
            })->filter(fn($event) => $event['start'] !== null)->values();

            $notifications = $user->notifications()->latest()->limit(10)->get();

            return view('healthworker.dashboard', compact(
                'user',
                'totalAppointments',
                'needingVitalsCount',
                'upcomingToday',
                'totalPatients',
                'recentAppointments',
                'calendarEvents',
                'notifications',
                'todaysAppointmentsList',
                'serviceStats'
            ));
        }

        // =====================================
        // 👤 PATIENT DASHBOARD
        // =====================================
        if ($user->isPatient()) {
            $totalAppointments    = $user->appointments()->count();
            $pendingAppointments  = $user->appointments()->where('status', 'pending')->count();
            $approvedAppointments = $user->appointments()->where('status', 'approved')->count();
            $rejectedAppointments = $user->appointments()->where('status', 'rejected')->count();

            $upcomingAppointments = Appointment::where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('booked_by', $user->id);
                })
                ->with(['slot', 'user'])
                ->whereDate('scheduled_at', '>=', today())
                ->whereNotIn('status', ['completed', 'cancelled', 'rejected', 'archived'])
                ->orderBy('scheduled_at')
                ->limit(5)
                ->get();

            $appointments = Appointment::where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('booked_by', $user->id);
                })
                ->with(['slot', 'user'])
                ->whereNotIn('status', ['completed', 'cancelled', 'rejected', 'archived'])
                ->orderBy('scheduled_at')
                ->get();

            $servicesMap = Service::all()->keyBy('name');
            $baseSlots = Slot::with('doctor')
                ->active()
                ->where('service', '!=', 'General Checkup')
                ->withCount(['appointments' => function ($q) {
                    $q->whereNotIn('status', ['cancelled', 'rejected']);
                }])
                ->orderBy('date', 'asc');

            $todayStr = today()->toDateString();

            // Fetch today's slots more robustly
            $todaySlots = Slot::with('doctor')
                ->whereDate('date', $todayStr)
                ->where('is_active', true)
                ->where('service', '!=', 'General Checkup')
                ->withCount(['appointments' => function ($q) {
                    $q->whereNotIn('status', ['cancelled', 'rejected']);
                }])
                ->get()
                ->filter(function ($slot) {
                    // 1. Capacity check: Total capacity - current appointments
                    $remainingSeats = (int)$slot->capacity - (int)$slot->appointments_count;
                    if ($remainingSeats <= 0) {
                        return false;
                    }
                    
                    // 2. Explicit available_spots column (if used)
                    if ($slot->available_spots === 0) {
                        return false;
                    }

                    // 3. Expiration check: Use a more permissive logic for today
                    // Only hide if the slot's end time was more than 1 hour ago
                    if ($slot->end_time && $slot->date) {
                        $expiryTime = $slot->date->copy()->setTimeFrom($slot->end_time)->addHour();
                        if ($expiryTime->isPast()) {
                            return false;
                        }
                    }

                    return true;
                })
                ->values();

            $futureSlots = Slot::with('doctor')
                ->whereDate('date', '>', $todayStr)
                ->where('is_active', true)
                ->where('service', '!=', 'General Checkup')
                ->withCount(['appointments' => function ($q) {
                    $q->whereNotIn('status', ['cancelled', 'rejected']);
                }])
                ->orderBy('date', 'asc')
                ->get()
                ->filter(function ($slot) use ($user) {
                    // Check capacity
                    $remainingSeats = (int)$slot->capacity - (int)$slot->appointments_count;
                    if ($remainingSeats <= 0 || $slot->available_spots === 0) {
                        return false;
                    }

                    // Don't show slots the user has already booked for themselves
                    $hasBooked = $slot->appointments()
                        ->where('user_id', $user->id)
                        ->whereNotIn('status', ['cancelled', 'rejected'])
                        ->exists();
                    
                    return !$hasBooked;
                })
                ->values()
                ->take(12);

            $activeAnnouncements = \App\Models\Announcement::where('status', 'active')
                ->where(function($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
                })
                ->latest()
                ->take(3)
                ->get();

            $notifications = $user->notifications()->latest()->limit(10)->get();
            $dependents = $user->dependents()->get();

            // Fetch Doctor Availabilities for Calendar (with safety check)
            $doctorAvailabilities = collect();
            if (\Illuminate\Support\Facades\Schema::hasTable('doctor_availabilities')) {
                // Fetch both recurring and one-time schedules
                // We ONLY want to show users whose role is explicitly 'doctor' and are active.
                // We also filter for 'scheduled', 'arrived', 'delayed' to show available days.
                $doctorAvailabilities = \App\Models\DoctorAvailability::whereHas('doctor', function($q) {
                        $q->whereRaw('LOWER(role) = ?', ['doctor'])
                          ->where('status', true); // Doctor account must be active
                    })
                    ->with(['doctor' => function($q) {
                        $q->select('id', 'first_name', 'last_name', 'role');
                    }])
                    ->where(function($q) {
                        $q->where('date', '>=', now()->startOfMonth())
                          ->orWhere('is_recurring', true);
                    })
                    ->whereIn('status', ['scheduled', 'arrived', 'delayed', 'absent'])
                    ->get()
                    ->map(function($a) {
                        // Normalize data for Alpine.js
                        $dayNames = [0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'];
                        
                        // Use explicit recurring_day or fallback to the date's day of week
                        $recurringDay = $a->recurring_day;
                        $dateDayOfWeek = $a->date ? (int)$a->date->dayOfWeek : null;

                        // For recurring entries, prefer explicit recurring_day; fallback to availability date day-of-week.
                        if ($a->is_recurring) {
                            if ($recurringDay === null || $dateDayOfWeek !== null && (int)$recurringDay !== $dateDayOfWeek) {
                                $recurringDay = $dateDayOfWeek;
                            }
                        }

                        $patternLabel = $a->is_recurring ? "(Every {$dayNames[$recurringDay]})" : "(One-time)";
                        
                        return [
                            'id' => $a->id,
                            'doctor_id' => $a->doctor_id,
                            'doctor_name' => "Dr. " . ($a->doctor ? $a->doctor->full_name : 'Unknown Doctor'),
                            'date' => $a->date ? $a->date->toDateString() : null,
                            'start_time' => $a->start_time,
                            'end_time' => $a->end_time,
                            'is_recurring' => (bool)$a->is_recurring,
                            'recurring_day' => $recurringDay !== null ? (int)$recurringDay : null,
                            'status' => $a->status,
                            'notes' => trim(($a->notes ?? '') . " " . $patternLabel)
                        ];
                    });
            }
            
            // Create a unified list of all possible patients (Account Holder + Dependents)
            $family = collect([$user])->merge($dependents)->map(function($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->id === auth()->id() ? 'Me (Account Holder)' : $p->first_name . ' (' . $p->relationship . ')',
                    'first_name' => $p->first_name,
                    'gender' => $p->gender,
                    'dob' => $p->dob ? $p->dob->format('Y-m-d') : null,
                ];
            });

            return view('patient.dashboard', compact(
                'user',
                'totalAppointments',
                'pendingAppointments',
                'approvedAppointments',
                'rejectedAppointments',
                'upcomingAppointments',
                'appointments',
                'todaySlots',
                'futureSlots',
                'activeAnnouncements',
                'notifications',
                'dependents',
                'family',
                'doctorAvailabilities'
            ));
        }

        // =====================================
        // 🧱 DEFAULT FALLBACK DASHBOARD
        // =====================================
        return view('dashboard.default', compact('user'));
    }
}
