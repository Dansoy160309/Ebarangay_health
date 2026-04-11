<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineDistribution;
use App\Models\MedicineSupply;
use App\Models\User;
use App\Notifications\MedicineExpiryAlertNotification;
use App\Notifications\MedicineLowStockAlertNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $query = Medicine::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('generic_name', 'like', "%{$search}%")
                    ->orWhere('brand_name', 'like', "%{$search}%");
            });
        }

        $medicines = $query->orderBy('generic_name')->paginate(15)->appends($request->query());

        $today = Carbon::today();
        $expiringSoonThreshold = $today->copy()->addDays(30);

        $lowStockMedicines = Medicine::whereColumn('stock', '<=', 'reorder_level')->get();

        $expiredMedicines = Medicine::whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<', $today)
            ->where('stock', '>', 0)
            ->get();

        $expiringTodayMedicines = Medicine::whereNotNull('expiration_date')
            ->whereDate('expiration_date', '=', $today)
            ->where('stock', '>', 0)
            ->get();

        $expiringSoonMedicines = Medicine::whereNotNull('expiration_date')
            ->whereDate('expiration_date', '>', $today)
            ->whereDate('expiration_date', '<=', $expiringSoonThreshold)
            ->where('stock', '>', 0)
            ->get();

        $expiringSupplyBatches = MedicineSupply::with('medicine')
            ->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '>=', $today)
            ->whereDate('expiration_date', '<=', $expiringSoonThreshold)
            ->orderBy('expiration_date')
            ->limit(5)
            ->get();

        $lowStockIds = $lowStockMedicines->pluck('id')->all();
        $expiredIds = $expiredMedicines->pluck('id')->all();
        $expiringTodayIds = $expiringTodayMedicines->pluck('id')->all();
        $expiringSoonIds = $expiringSoonMedicines->pluck('id')->all();

        // Send deduplicated in-app notifications for medicine alerts.
        $this->notifyAdminsForLowStockMedicines($lowStockMedicines);
        $this->notifyAdminsForExpiringMedicines($expiredMedicines, 'expired');
        $this->notifyAdminsForExpiringMedicines(
            $expiringTodayMedicines->merge($expiringSoonMedicines)->unique('id')->values(),
            'expiring_soon'
        );

        return view('admin.medicines.index', compact(
            'medicines',
            'lowStockIds',
            'expiredIds',
            'expiringTodayIds',
            'expiringSoonIds',
            'expiringSupplyBatches'
        ));
    }

    public function create()
    {
        return view('admin.medicines.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'generic_name' => 'required|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'dosage_form' => ['required', 'string', Rule::in(['Tablet', 'Capsule', 'Syrup'])],
            'strength' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'expiration_date' => 'nullable|date',
        ]);

        if ($this->hasDuplicateMedicine($data['generic_name'], $data['strength'] ?? null)) {
            return back()
                ->withErrors([
                    'generic_name' => 'This medicine already exists with the same dosage/strength. Use a different dosage or update the existing record.',
                ])
                ->withInput();
        }

        $initialStock = $data['stock'] ?? 0;
        unset($data['stock']);

        DB::transaction(function () use ($data, $initialStock) {
            $medicine = Medicine::create($data + ['stock' => 0]);

            if ($initialStock > 0) {
                MedicineSupply::create([
                    'medicine_id' => $medicine->id,
                    'batch_number' => null,
                    'quantity' => $initialStock,
                    'expiration_date' => $medicine->expiration_date,
                    'supplier_name' => 'Initial Stock',
                    'date_received' => now()->toDateString(),
                    'received_by' => auth()->id(),
                ]);

                $medicine->increment('stock', $initialStock);
            }
        });

        return redirect()->route('admin.medicines.index')->with('success', 'Medicine saved.');
    }

    public function edit(Medicine $medicine)
    {
        return view('admin.medicines.edit', compact('medicine'));
    }

    public function update(Request $request, Medicine $medicine)
    {
        $data = $request->validate([
            'generic_name' => 'required|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'dosage_form' => ['required', 'string', Rule::in(['Tablet', 'Capsule', 'Syrup'])],
            'strength' => 'nullable|string|max:255',
            'reorder_level' => 'required|integer|min:0',
            'expiration_date' => 'nullable|date',
        ]);

        if ($this->hasDuplicateMedicine($data['generic_name'], $data['strength'] ?? null, $medicine->id)) {
            return back()
                ->withErrors([
                    'generic_name' => 'Another medicine already uses this name and dosage/strength. Please change the dosage or edit that existing entry instead.',
                ])
                ->withInput();
        }

        $medicine->update($data);

        return redirect()->route('admin.medicines.index')->with('success', 'Medicine updated.');
    }

    public function destroy(Medicine $medicine)
    {
        $hasDistributionHistory = MedicineDistribution::where('medicine_id', $medicine->id)->exists();
        $hasSupplyHistory = MedicineSupply::where('medicine_id', $medicine->id)->exists();

        if ($hasDistributionHistory || $hasSupplyHistory) {
            return redirect()->back()->with('error', 'Cannot delete medicine with stock/supply/distribution history.');
        }

        $medicine->delete();

        return redirect()->route('admin.medicines.index')->with('success', 'Medicine deleted successfully.');
    }

    public function distributions(Request $request)
    {
        $query = MedicineDistribution::with(['medicine', 'patient', 'midwife']);

        if ($request->filled('date_from')) {
            $query->whereDate('distributed_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('distributed_at', '<=', $request->date_to);
        }

        if ($request->filled('medicine_id')) {
            $query->where('medicine_id', $request->medicine_id);
        }

        $distributions = $query->orderByDesc('distributed_at')->paginate(20)->appends($request->query());
        $medicines = Medicine::orderBy('generic_name')->get();

        return view('admin.medicines.distributions', compact('distributions', 'medicines'));
    }

    public function reports(Request $request)
    {
        $type = $request->input('type', 'daily');

        $baseQuery = MedicineDistribution::with('medicine');
        $supplyQuery = MedicineSupply::with('medicine');

        $date = null;
        $year = null;
        $month = null;

        if ($type === 'daily') {
            $date = $request->input('date', Carbon::today()->toDateString());
            $baseQuery->whereDate('distributed_at', $date);
            $supplyQuery->whereDate('date_received', $date);
        } elseif ($type === 'monthly') {
            $year = (int) $request->input('year', Carbon::today()->year);
            $month = (int) $request->input('month', Carbon::today()->month);
            $baseQuery->whereYear('distributed_at', $year)->whereMonth('distributed_at', $month);
            $supplyQuery->whereYear('date_received', $year)->whereMonth('date_received', $month);
        }

        $distributions = $baseQuery->get();
        $supplies = $supplyQuery->get();

        $usageByMedicine = $distributions->groupBy('medicine_id')->map(function ($group) {
            return [
                'medicine' => $group->first()->medicine,
                'total_quantity' => $group->sum('quantity'),
                'total_doses' => $group->count(),
            ];
        });

        $supplyByMedicine = $supplies->groupBy('medicine_id')->map(function ($group) {
            return [
                'medicine' => $group->first()->medicine,
                'total_quantity' => $group->sum('quantity'),
                'total_deliveries' => $group->count(),
            ];
        });

        $inventorySummary = Medicine::orderBy('generic_name')->get();

        $totalMedicines = $inventorySummary->count();
        $totalStock = $inventorySummary->sum('stock');

        $distributedInPeriod = $distributions->sum('quantity');

        $today = Carbon::today();

        $lowStockCount = $inventorySummary
            ->filter(function ($medicine) {
                return $medicine->stock <= $medicine->reorder_level;
            })
            ->count();

        $expiringSoonThreshold = $today->copy()->addDays(30);

        $expiringSoonCount = $inventorySummary
            ->filter(function ($medicine) use ($expiringSoonThreshold) {
                return $medicine->expiration_date && $medicine->expiration_date->lte($expiringSoonThreshold);
            })
            ->count();

        if ($type === 'daily') {
            $selectedDate = Carbon::parse($date);
            $stockStart = $selectedDate->copy()->subDays(6);
            $stockEnd = $selectedDate->copy();
        } else {
            $selectedMonth = Carbon::create($year, $month, 1);
            $stockStart = $selectedMonth->copy();
            $stockEnd = $selectedMonth->copy()->endOfMonth();
        }

        $stockSupplyData = MedicineSupply::whereBetween('date_received', [$stockStart->toDateString(), $stockEnd->toDateString()])
            ->selectRaw('DATE(date_received) as d, SUM(quantity) as total')
            ->groupBy('d')
            ->pluck('total', 'd');

        $stockDistributionData = MedicineDistribution::whereBetween('distributed_at', [$stockStart->toDateString(), $stockEnd->toDateString()])
            ->selectRaw('DATE(distributed_at) as d, SUM(quantity) as total')
            ->groupBy('d')
            ->pluck('total', 'd');

        $stockChartLabels = [];
        $stockChartData = [];
        $cumulative = 0;

        for ($cursor = $stockStart->copy(); $cursor->lte($stockEnd); $cursor->addDay()) {
            $dateKey = $cursor->toDateString();
            $netChange = ($stockSupplyData[$dateKey] ?? 0) - ($stockDistributionData[$dateKey] ?? 0);
            $cumulative += $netChange;
            $stockChartLabels[] = $cursor->format('M d');
            $stockChartData[] = $cumulative;
        }

        $usageChartLabels = $usageByMedicine->map(function ($row) {
            return $row['medicine']?->generic_name ?? 'Unknown';
        })->values();

        $usageChartData = $usageByMedicine->map(function ($row) {
            return $row['total_quantity'];
        })->values();

        return view('admin.medicines.reports', [
            'type' => $type,
            'selectedDate' => $date,
            'selectedMonth' => $month,
            'selectedYear' => $year,
            'distributions' => $distributions,
            'usageByMedicine' => $usageByMedicine,
            'inventorySummary' => $inventorySummary,
            'supplies' => $supplies,
            'supplyByMedicine' => $supplyByMedicine,
            'totalMedicines' => $totalMedicines,
            'totalStock' => $totalStock,
            'distributedInPeriod' => $distributedInPeriod,
            'lowStockCount' => $lowStockCount,
            'expiringSoonCount' => $expiringSoonCount,
            'stockChartLabels' => $stockChartLabels,
            'stockChartData' => $stockChartData,
            'usageChartLabels' => $usageChartLabels,
            'usageChartData' => $usageChartData,
        ]);
    }

    /**
     * Export Medicine Reports to CSV
     */
    public function exportExcel(Request $request)
    {
        $type = $request->input('type', 'daily');
        $date = $request->input('date', Carbon::today()->toDateString());
        $year = $request->input('year', Carbon::today()->year);
        $month = $request->input('month', Carbon::today()->month);

        $filename = "Medicine_Report_{$type}_" . ($type === 'daily' ? $date : "{$year}_{$month}") . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($type, $date, $year, $month) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['MEDICINE REPORT - ' . strtoupper($type)]);
            fputcsv($file, ['Period:', $type === 'daily' ? $date : Carbon::create()->month($month)->format('F') . ' ' . $year]);
            fputcsv($file, []);

            // 1. Distribution Summary
            fputcsv($file, ['--- DISTRIBUTION SUMMARY ---']);
            fputcsv($file, ['Medicine', 'Generic Name', 'Brand Name', 'Total Units', 'Doses/Patients']);
            
            $distQuery = MedicineDistribution::with('medicine');
            if ($type === 'daily') {
                $distQuery->whereDate('distributed_at', $date);
            } else {
                $distQuery->whereYear('distributed_at', $year)->whereMonth('distributed_at', $month);
            }
            
            $usage = $distQuery->get()->groupBy('medicine_id')->map(function ($group) {
                return [
                    'medicine' => $group->first()->medicine,
                    'qty' => $group->sum('quantity'),
                    'count' => $group->count(),
                ];
            });

            foreach($usage as $row) {
                fputcsv($file, [
                    $row['medicine']->generic_name,
                    $row['medicine']->generic_name,
                    $row['medicine']->brand_name ?? 'Generic',
                    $row['qty'],
                    $row['count']
                ]);
            }
            fputcsv($file, []);

            // 2. Supply Summary
            fputcsv($file, ['--- SUPPLY SUMMARY ---']);
            fputcsv($file, ['Medicine', 'Generic Name', 'Brand Name', 'Total Qty Received', 'Batches']);
            
            $supplyQuery = MedicineSupply::with('medicine');
            if ($type === 'daily') {
                $supplyQuery->whereDate('date_received', $date);
            } else {
                $supplyQuery->whereYear('date_received', $year)->whereMonth('date_received', $month);
            }
            
            $supplies = $supplyQuery->get()->groupBy('medicine_id')->map(function ($group) {
                return [
                    'medicine' => $group->first()->medicine,
                    'qty' => $group->sum('quantity'),
                    'count' => $group->count(),
                ];
            });

            foreach($supplies as $row) {
                fputcsv($file, [
                    $row['medicine']->generic_name,
                    $row['medicine']->generic_name,
                    $row['medicine']->brand_name ?? 'Generic',
                    $row['qty'],
                    $row['count']
                ]);
            }
            fputcsv($file, []);

            // 3. Current Inventory
            fputcsv($file, ['--- CURRENT INVENTORY STATUS ---']);
            fputcsv($file, ['Medicine', 'Brand', 'Current Stock', 'Reorder Level', 'Status']);
            
            $medicines = Medicine::orderBy('generic_name')->get();
            foreach($medicines as $m) {
                $status = $m->stock <= $m->reorder_level ? 'LOW STOCK' : 'Normal';
                fputcsv($file, [
                    $m->generic_name,
                    $m->brand_name ?? 'Generic',
                    $m->stock,
                    $m->reorder_level,
                    $status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function supplies(Request $request)
    {
        $query = MedicineSupply::with(['medicine', 'receiver']);

        if ($request->filled('date_from')) {
            $query->whereDate('date_received', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date_received', '<=', $request->date_to);
        }

        if ($request->filled('medicine_id')) {
            $query->where('medicine_id', $request->medicine_id);
        }

        if ($request->filled('supplier_name')) {
            $query->where('supplier_name', 'like', '%' . $request->supplier_name . '%');
        }

        $supplies = $query->orderByDesc('date_received')->paginate(20)->appends($request->query());
        $medicines = Medicine::orderBy('generic_name')->get();

        return view('admin.medicines.supplies', compact('supplies', 'medicines'));
    }

    public function createSupply()
    {
        $medicines = Medicine::orderBy('generic_name')->get();

        return view('admin.medicines.supply-create', compact('medicines'));
    }

    public function storeSupply(Request $request)
    {
        $data = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'batch_number' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
            'expiration_date' => 'nullable|date|after:today',
            'supplier_name' => 'nullable|string|max:255',
            'date_received' => 'required|date',
        ]);

        $medicine = Medicine::findOrFail($data['medicine_id']);

        DB::transaction(function () use ($data, $medicine) {
            MedicineSupply::create([
                'medicine_id' => $medicine->id,
                'batch_number' => $data['batch_number'] ?? null,
                'quantity' => $data['quantity'],
                'expiration_date' => $data['expiration_date'] ?? null,
                'supplier_name' => $data['supplier_name'] ?? null,
                'date_received' => $data['date_received'],
                'received_by' => auth()->id(),
            ]);

            $medicine->increment('stock', $data['quantity']);
        });

        return redirect()->route('admin.medicines.supplies')
            ->with('success', 'Supply recorded and stock updated.');
    }

    private function notifyAdminsForLowStockMedicines($medicines): void
    {
        if ($medicines->isEmpty()) {
            return;
        }

        $admins = User::where('role', 'admin')->get();

        foreach ($medicines as $medicine) {
            foreach ($admins as $admin) {
                $alreadyNotifiedToday = $admin->notifications()
                    ->where('type', MedicineLowStockAlertNotification::class)
                    ->where('data->medicine_id', $medicine->id)
                    ->whereDate('created_at', now()->toDateString())
                    ->exists();

                if (!$alreadyNotifiedToday) {
                    $admin->notify(new MedicineLowStockAlertNotification($medicine));
                }
            }
        }
    }

    private function notifyAdminsForExpiringMedicines($medicines, string $status): void
    {
        if ($medicines->isEmpty()) {
            return;
        }

        $admins = User::where('role', 'admin')->get();

        foreach ($medicines as $medicine) {
            if (!$medicine->expiration_date) {
                continue;
            }

            foreach ($admins as $admin) {
                $alreadyNotifiedToday = $admin->notifications()
                    ->where('type', MedicineExpiryAlertNotification::class)
                    ->where('data->medicine_id', $medicine->id)
                    ->where('data->status', $status)
                    ->whereDate('created_at', now()->toDateString())
                    ->exists();

                if (!$alreadyNotifiedToday) {
                    $admin->notify(new MedicineExpiryAlertNotification($medicine, $status));
                }
            }
        }
    }

    private function hasDuplicateMedicine(string $genericName, ?string $strength, ?int $ignoreId = null): bool
    {
        $normalizedName = $this->normalizeMedicineName($genericName);
        $normalizedStrength = $this->normalizeMedicineStrength($strength);

        $query = Medicine::query()
            ->select(['id', 'generic_name', 'strength'])
            ->whereRaw('LOWER(TRIM(generic_name)) = ?', [$normalizedName]);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->get()->contains(function (Medicine $medicine) use ($normalizedStrength) {
            return $this->normalizeMedicineStrength($medicine->strength) === $normalizedStrength;
        });
    }

    private function normalizeMedicineName(string $value): string
    {
        return preg_replace('/\s+/', ' ', strtolower(trim($value))) ?? '';
    }

    private function normalizeMedicineStrength(?string $value): string
    {
        return preg_replace('/\s+/', '', strtolower(trim((string) $value))) ?? '';
    }
}
