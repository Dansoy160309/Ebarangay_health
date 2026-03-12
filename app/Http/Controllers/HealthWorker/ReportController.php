<?php

namespace App\Http\Controllers\HealthWorker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Patient;
use App\Models\HealthRecord;
use App\Models\Service;
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
        ];

        // 3. Appointment Summary (Previous Period) for Growth Calc
        $prevAppointmentStats = [
            'total'     => Appointment::whereBetween('scheduled_at', [$previousStart, $previousEnd])->count(),
            'pending'   => Appointment::whereBetween('scheduled_at', [$previousStart, $previousEnd])->where('status', 'pending')->count(),
            'approved'  => Appointment::whereBetween('scheduled_at', [$previousStart, $previousEnd])->where('status', 'approved')->count(),
            'completed' => Appointment::whereBetween('scheduled_at', [$previousStart, $previousEnd])->where('status', 'completed')->count(),
            'cancelled' => Appointment::whereBetween('scheduled_at', [$previousStart, $previousEnd])->where('status', 'cancelled')->count(),
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
        $serviceStats = Appointment::selectRaw('service, COUNT(*) as count')
            ->whereBetween('scheduled_at', [$startDate, $endDate])
            ->groupBy('service')
            ->orderByDesc('count')
            ->get();

        // 6. Patient Demographics (Age Groups)
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

        // 7. Patient Demographics (Gender)
        $genderStats = Patient::query()
            ->selectRaw('gender, COUNT(*) as count')
            ->groupBy('gender')
            ->pluck('count', 'gender');

        // 8. Common Illnesses (Top 10 Diagnoses)
        $diseaseStats = HealthRecord::select('diagnosis', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('diagnosis')
            ->where('diagnosis', '!=', '')
            ->groupBy('diagnosis')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // 9. Prenatal Cases by Purok (Active/Recent)
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

        // 10. Immunization Coverage (Vaccine Types)
        $immunizationRecords = HealthRecord::whereHas('service', function($q) {
                $q->where('name', 'like', '%Immunization%');
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $vaccineStats = [];
        foreach ($immunizationRecords as $record) {
            // metadata is cast to array in model
            if (isset($record->metadata['vaccine_name'])) {
                $vaccine = $record->metadata['vaccine_name'];
                if (!isset($vaccineStats[$vaccine])) {
                    $vaccineStats[$vaccine] = 0;
                }
                $vaccineStats[$vaccine]++;
            }
        }
        arsort($vaccineStats);
        $vaccineStats = array_slice($vaccineStats, 0, 10);

        // 11. Hypertension / BP Analytics
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

        return view('healthworker.reports.index', compact(
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
            'endDate'
        ));
    }
}
