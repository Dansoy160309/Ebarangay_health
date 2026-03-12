<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Patient;
use App\Models\HealthRecord;
use App\Models\Service;
use App\Models\PatientProfile;
use App\Models\Medicine;
use App\Models\MedicineDistribution;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Determine Date Range based on 'period' or custom dates
        $period = $request->input('period', 'month'); // Default to 'month'
        
        switch ($period) {
            case 'today':
                $startDate = now()->format('Y-m-d');
                $endDate = now()->format('Y-m-d');
                break;
            case 'week':
                $startDate = now()->startOfWeek()->format('Y-m-d');
                $endDate = now()->endOfWeek()->format('Y-m-d');
                break;
            case 'month':
                $startDate = now()->startOfMonth()->format('Y-m-d');
                $endDate = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'year':
                $startDate = now()->startOfYear()->format('Y-m-d');
                $endDate = now()->endOfYear()->format('Y-m-d');
                break;
            case 'custom':
            default:
                $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
                $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
                break;
        }

        // Calculate Previous Period for Comparison
        $currentStart = Carbon::parse($startDate);
        $currentEnd = Carbon::parse($endDate);
        $daysDiff = $currentStart->diffInDays($currentEnd) + 1;
        
        $previousEnd = $currentStart->copy()->subDay();
        $previousStart = $previousEnd->copy()->subDays($daysDiff - 1);

        // 2. Appointment Summary (Current Period)
        $appointmentStats = [
            'total'     => Appointment::whereBetween('scheduled_at', [$startDate, $endDate])->count(),
            'pending'   => Appointment::whereBetween('scheduled_at', [$startDate, $endDate])->where('status', 'pending')->count(),
            'approved'  => Appointment::whereBetween('scheduled_at', [$startDate, $endDate])->where('status', 'approved')->count(),
            'completed' => Appointment::whereBetween('scheduled_at', [$startDate, $endDate])->where('status', 'completed')->count(),
            'cancelled' => Appointment::whereBetween('scheduled_at', [$startDate, $endDate])->where('status', 'cancelled')->count(),
            'no_show'   => Appointment::whereBetween('scheduled_at', [$startDate, $endDate])->where('status', 'no_show')->count(),
        ];

        // 3. Slot Utilization Stats
        $slotStats = [
            'total_slots_created' => Slot::whereBetween('date', [$startDate, $endDate])->sum('capacity'),
            'total_slots_booked'  => Appointment::whereHas('slot', function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            })->whereNotIn('status', ['cancelled', 'rejected'])->count(),
        ];

        // 4. Peak Booking Times (By Hour)
        $peakBookingTimes = Appointment::selectRaw('HOUR(scheduled_at) as hour, COUNT(*) as count')
            ->whereBetween('scheduled_at', [$startDate, $endDate])
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                $item->formatted_hour = Carbon::createFromTime($item->hour, 0)->format('g:i A');
                return $item;
            });

        // 5. Appointment Summary (Previous Period) for Growth Calc
        $prevAppointmentStats = [
            'total'     => Appointment::whereBetween('scheduled_at', [$previousStart, $previousEnd])->count(),
            'pending'   => Appointment::whereBetween('scheduled_at', [$previousStart, $previousEnd])->where('status', 'pending')->count(),
            'approved'  => Appointment::whereBetween('scheduled_at', [$previousStart, $previousEnd])->where('status', 'approved')->count(),
            'completed' => Appointment::whereBetween('scheduled_at', [$previousStart, $previousEnd])->where('status', 'completed')->count(),
            'cancelled' => Appointment::whereBetween('scheduled_at', [$previousStart, $previousEnd])->where('status', 'cancelled')->count(),
            'no_show'   => Appointment::whereBetween('scheduled_at', [$previousStart, $previousEnd])->where('status', 'no_show')->count(),
        ];

        // Calculate Growth
        $growthStats = [];
        foreach ($appointmentStats as $key => $currentValue) {
            $prevValue = $prevAppointmentStats[$key];
            if ($prevValue > 0) {
                $growth = (($currentValue - $prevValue) / $prevValue) * 100;
            } else {
                $growth = $currentValue > 0 ? 100 : 0;
            }
            $growthStats[$key] = round($growth, 1);
        }

        // 4. Appointment Trends (Dynamic Grouping)
        // If range <= 90 days, show Daily/Weekly trend (Group by Date)
        // If range > 90 days, show Monthly trend (Group by Month)
        
        if ($daysDiff <= 90) {
            $trendStats = Appointment::selectRaw('DATE(scheduled_at) as label, COUNT(*) as count')
                ->whereBetween('scheduled_at', [$startDate, $endDate])
                ->groupBy('label')
                ->orderBy('label')
                ->get()
                ->map(function ($item) {
                    $item->label = Carbon::parse($item->label)->format('M d'); // e.g. "Oct 05"
                    return $item;
                });
            $trendLabel = 'Daily Appointments';
        } else {
            $trendStats = Appointment::selectRaw('DATE_FORMAT(scheduled_at, "%Y-%m") as sort_key, MONTHNAME(scheduled_at) as label, COUNT(*) as count')
                ->whereBetween('scheduled_at', [$startDate, $endDate])
                ->groupBy('sort_key', 'label')
                ->orderBy('sort_key')
                ->get();
            $trendLabel = 'Monthly Appointments';
        }

        // 5. Service Usage

        // 3. Service Usage
        $serviceStats = Appointment::selectRaw('service, COUNT(*) as count')
            ->whereBetween('scheduled_at', [$startDate, $endDate])
            ->groupBy('service')
            ->orderByDesc('count')
            ->get();

        // 4. Patient Demographics (Age Groups) - This is usually a snapshot of CURRENT patients, not time-bound.
        // Unless "New Patients Registered". Let's keep it as All Active Patients for now.
        $patients = Patient::all();
        $ageGroups = [
            'Children (0-12)' => 0,
            'Teens (13-19)'   => 0,
            'Adults (20-59)'  => 0,
            'Seniors (60+)'   => 0,
        ];

        foreach ($patients as $patient) {
            if ($patient->dob) {
                $age = Carbon::parse($patient->dob)->age;
                if ($age <= 12) $ageGroups['Children (0-12)']++;
                elseif ($age <= 19) $ageGroups['Teens (13-19)']++;
                elseif ($age <= 59) $ageGroups['Adults (20-59)']++;
                else $ageGroups['Seniors (60+)']++;
            }
        }

        // 5. Patient Demographics (Gender)
        $genderStats = Patient::query()
            ->selectRaw('gender, COUNT(*) as count')
            ->groupBy('gender')
            ->pluck('count', 'gender');

        // 6. Common Illnesses (Top 10 Diagnoses) - Time Bound
        $diseaseStats = HealthRecord::select('diagnosis', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('diagnosis')
            ->where('diagnosis', '!=', '')
            ->groupBy('diagnosis')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // 7. Prenatal Cases by Purok (Active/Recent) - Time Bound based on record creation
        $prenatalStats = Patient::query()
            ->whereHas('healthRecords', function($q) use ($startDate, $endDate) {
                $q->whereHas('service', function($sq) {
                    $sq->where('name', 'Prenatal');
                })
                ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->selectRaw('purok, COUNT(*) as count')
            ->groupBy('purok')
            ->pluck('count', 'purok');

        // 8. Immunization Coverage (Vaccine Types)
        // Extract vaccine_name from metadata JSON
        // Note: JSON extraction syntax depends on DB. For MySQL 5.7+: ->> or JSON_UNQUOTE(JSON_EXTRACT(...))
        // We will fetch records and process in PHP to be safe and compatible.
        $immunizationRecords = HealthRecord::whereHas('service', function($q) {
                $q->where('name', 'like', '%Immunization%');
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $vaccineStats = [];
        foreach ($immunizationRecords as $record) {
            // metadata is already cast to array in model
            if (isset($record->metadata['vaccine_name'])) {
                $vaccine = $record->metadata['vaccine_name'];
                if (!isset($vaccineStats[$vaccine])) {
                    $vaccineStats[$vaccine] = 0;
                }
                $vaccineStats[$vaccine]++;
            }
        }
        arsort($vaccineStats); // Sort by count desc
        $vaccineStats = array_slice($vaccineStats, 0, 10); // Top 10

        // 9. Hypertension / BP Analytics
        // Scan vital_signs for BP readings
        $bpRecords = HealthRecord::whereNotNull('vital_signs')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $bpStats = [
            'Normal' => 0,
            'Elevated' => 0,
            'High Stage 1' => 0,
            'High Stage 2' => 0,
        ];

        foreach ($bpRecords as $record) {
            $systolic = 0;
            $diastolic = 0;
            $found = false;

            // Handle Array (New Format)
            if (is_array($record->vital_signs) && isset($record->vital_signs['blood_pressure'])) {
                $parts = explode('/', $record->vital_signs['blood_pressure']);
                if (count($parts) >= 2) {
                    $systolic = (int)$parts[0];
                    $diastolic = (int)$parts[1];
                    $found = true;
                }
            } 
            // Handle String (Legacy/Fallback)
            elseif (is_string($record->vital_signs)) {
                if (preg_match('/BP:\s*(\d+)\s*\/\s*(\d+)/i', $record->vital_signs, $matches)) {
                    $systolic = (int)$matches[1];
                    $diastolic = (int)$matches[2];
                    $found = true;
                }
            }

            if ($found) {
                if ($systolic < 120 && $diastolic < 80) {
                    $bpStats['Normal']++;
                } elseif ($systolic >= 140 || $diastolic >= 90) {
                    $bpStats['High Stage 2']++;
                } elseif ($systolic >= 130 || $diastolic >= 80) {
                    $bpStats['High Stage 1']++;
                } else {
                    $bpStats['Elevated']++;
                }
            }
        }

        // 🤰 Maternal Health Stats
        $maternalStats = [
            'high_risk_count' => PatientProfile::where('is_high_risk', true)->count(),
            'active_pregnancies' => PatientProfile::whereNotNull('edd')
                ->where('edd', '>=', now())
                ->count(),
        ];

        // 💉 Immunization Stats
        $immunizationStats = [
            'fully_immunized' => PatientProfile::where('is_fully_immunized', true)->count(),
            'total_children' => User::where('role', 'patient')
                ->where('dob', '>=', now()->subYears(5))
                ->count(),
        ];

        // 💊 Medicine Stats
        $medicineStats = [
            'total_stock' => Medicine::sum('stock'),
            'low_stock_count' => Medicine::whereRaw('stock <= reorder_level')->count(),
        ];

        return view('admin.reports.index', compact(
            'appointmentStats',
            'trendStats',
            'trendLabel',
            'serviceStats',
            'ageGroups',
            'genderStats',
            'diseaseStats',
            'prenatalStats',
            'vaccineStats',
            'bpStats',
            'growthStats',
            'startDate',
            'endDate',
            'maternalStats',
            'immunizationStats',
            'medicineStats',
            'slotStats',
            'peakBookingTimes'
        ));
    }

    /**
     * Calendar View for Slots, Bookings, and Attendance
     */
    public function calendar(Request $request)
    {
        $viewType = $request->input('view_type', 'all_events'); // 'all_events', 'slots_only', 'appointments_only'

        $slotsQuery = Slot::with(['doctor', 'appointments.user'])
            ->where('date', '>=', now()->subMonths(3));

        $slots = $slotsQuery->get();

        $events = [];
        $groupedAppointments = [];

        // First, group appointments by slot_id
        foreach ($slots as $slot) {
            foreach ($slot->appointments as $appointment) {
                if ($appointment->status === 'cancelled' || $appointment->status === 'rejected') continue;
                
                if (!isset($groupedAppointments[$slot->id])) {
                    $groupedAppointments[$slot->id] = [
                        'slot' => $slot,
                        'appointments' => [],
                        'statuses' => [],
                    ];
                }
                
                $isPast = $appointment->scheduled_at->isPast();
                $effectiveStatus = $appointment->status;
                if (in_array($appointment->status, ['approved', 'rescheduled']) && $isPast) {
                    $effectiveStatus = 'no_show';
                }

                $groupedAppointments[$slot->id]['appointments'][] = $appointment;
                $groupedAppointments[$slot->id]['statuses'][] = $effectiveStatus;
            }
        }

        // Process Slots and Grouped Appointments for the calendar
        if ($viewType === 'all_events' || $viewType === 'slots_only') {
            foreach ($slots as $slot) {
                $events[] = [
                    'id'    => 'slot-' . $slot->id,
                    'title' => $slot->service . ' (Slot)',
                    'start' => $slot->date->format('Y-m-d') . 'T' . $slot->start_time->format('H:i:s'),
                    'end'   => $slot->date->format('Y-m-d') . 'T' . $slot->end_time->format('H:i:s'),
                    'color' => '#3b82f6', // blue-500
                    'extendedProps' => [
                        'type'     => 'slot',
                        'capacity' => $slot->capacity,
                        'booked'   => $slot->bookedCount(),
                        'doctor'   => $slot->doctor ? $slot->doctor->full_name : 'No doctor assigned',
                    ]
                ];
            }
        }

        if ($viewType === 'all_events' || $viewType === 'appointments_only') {
            foreach ($groupedAppointments as $groupId => $group) {
                $slot = $group['slot'];
                $count = count($group['appointments']);
                $patientNames = collect($group['appointments'])->map(fn($a) => $a->user->full_name)->implode(', ');

                // Determine the collective status color
                $color = '#fbbf24'; // Default yellow for pending/mixed
                if (collect($group['statuses'])->every(fn($s) => $s === 'completed')) {
                    $color = '#10b981'; // All completed = green
                } elseif (collect($group['statuses'])->every(fn($s) => $s === 'no_show')) {
                    $color = '#f87171'; // All no-show = red
                }

                $events[] = [
                    'id'    => 'group-' . $groupId,
                    'title' => "($count) " . $slot->service,
                    'start' => $slot->date->format('Y-m-d') . 'T' . $slot->start_time->format('H:i:s'),
                    'end'   => $slot->date->format('Y-m-d') . 'T' . $slot->end_time->format('H:i:s'),
                    'color' => $color,
                    'extendedProps' => [
                        'type'     => 'appointment_group',
                        'patient_count' => $count,
                        'patient_names' => $patientNames,
                        'service' => $slot->service,
                        'time' => $slot->start_time->format('g:i A'),
                        'appointments' => collect($group['appointments'])->map(function($a) {
                            $isPast = $a->scheduled_at->isPast();
                            $effectiveStatus = $a->status;
                            if (in_array($a->status, ['approved', 'rescheduled']) && $isPast) {
                                $effectiveStatus = 'no_show';
                            }

                            return [
                                'id' => $a->id,
                                'patient_name' => $a->user->full_name,
                                'patient_photo' => $a->user->profile_photo_url,
                                'status' => $effectiveStatus,
                                'has_vitals' => (bool)$a->healthRecord?->vital_signs,
                                'service' => $a->service,
                                'reason' => $a->reason ?? 'No specific reason provided',
                                'scheduled_at' => $a->scheduled_at->format('M d, Y \a\t g:i A'),
                                'vitals' => $a->healthRecord?->vital_signs,
                                'provider' => $a->slot?->doctor ? $a->slot->doctor->full_name : 'No doctor assigned',
                            ];
                        })->all(),
                    ]
                ];
            }
        }

        return view('admin.reports.calendar', compact('events', 'viewType'));
    }

    /**
     * DOH FHSIS Monthly Summary Report
     */
    public function fhsisSummary(Request $request)
    {
        $month = $request->input('month', now()->format('m'));
        $year = $request->input('year', now()->format('Y'));
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // 🤰 Maternal Health Indicators
        $maternal = [
            'total_prenatal_visits' => Appointment::where('service', 'Prenatal')
                ->whereBetween('scheduled_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->count(),
            'high_risk_pregnancies' => PatientProfile::where('is_high_risk', true)
                ->whereHas('user', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                })->count(),
            'postpartum_checkups' => Appointment::where('service', 'like', '%Postpartum%')
                ->whereBetween('scheduled_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->count(),
        ];

        // 💉 Child Immunization Indicators
        $immunization = [
            'fic_count' => PatientProfile::where('is_fully_immunized', true)
                ->whereHas('user', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate])
                      ->where('dob', '>=', now()->subYear());
                })->count(),
            'total_infants' => User::where('role', 'patient')
                ->where('dob', '>=', now()->subYear())
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
        ];

        // 🧬 Morbidity (Top 5 Diseases for the month)
        $morbidity = HealthRecord::select('diagnosis', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('diagnosis')
            ->groupBy('diagnosis')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return view('admin.reports.fhsis', compact('maternal', 'immunization', 'morbidity', 'month', 'year', 'startDate'));
    }

    /**
     * Generate Monthly Vaccine Supply & Usage Summary (DOH Format)
     */
    public function vaccineSummary(Request $request)
    {
        $month = $request->input('month', now()->format('m'));
        $year = $request->input('year', now()->format('Y'));
        
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // 1. Get Vaccine Usage Data
        $usage = \App\Models\HealthRecord::whereBetween('verified_at', [$startDate, $endDate])
            ->whereNotNull('metadata->vaccine_batch_id')
            ->get()
            ->groupBy('metadata->vaccine_given');

        $summary = [];
        $vaccines = \App\Models\Vaccine::with(['batches' => function($q) use ($endDate) {
            $q->where('received_at', '<=', $endDate);
        }])->get();

        foreach ($vaccines as $vaccine) {
            $vaccineName = $vaccine->name;
            
            // Total doses received this month or earlier
            $totalReceived = $vaccine->batches->sum('quantity_received');
            
            // Total doses administered this month
            $administeredThisMonth = \App\Models\HealthRecord::whereBetween('verified_at', [$startDate, $endDate])
                ->where('metadata->vaccine_given', 'like', "%{$vaccineName}%")
                ->count();

            // Total doses administered before this month
            $administeredBefore = \App\Models\HealthRecord::where('verified_at', '<', $startDate)
                ->where('metadata->vaccine_given', 'like', "%{$vaccineName}%")
                ->count();

            $summary[] = [
                'name' => $vaccineName,
                'manufacturer' => $vaccine->manufacturer,
                'beginning_balance' => $totalReceived - $administeredBefore,
                'received_this_month' => $vaccine->batches->whereBetween('received_at', [$startDate, $endDate])->sum('quantity_received'),
                'administered' => $administeredThisMonth,
                'remaining' => $totalReceived - $administeredBefore - $administeredThisMonth,
                'wastage' => 0, // Placeholder for future enhancement
            ];
        }

        return view('admin.reports.vaccines', compact('summary', 'month', 'year'));
    }
}
