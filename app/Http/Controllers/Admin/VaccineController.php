<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vaccine;
use App\Models\VaccineBatch;
use App\Models\VaccineAdministration;

class VaccineController extends Controller
{
    public function index(Request $request)
    {
        $query = Vaccine::with(['batches' => function($q) {
            $q->orderBy('expiry_date', 'asc');
        }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('manufacturer', 'like', "%{$search}%");
        }

        $vaccines = $query->paginate(15);

        $lowStockVaccines = Vaccine::all()->filter(fn($v) => $v->in_stock_quantity <= $v->min_stock_level);
        $nearExpiryBatches = VaccineBatch::with('vaccine')
            ->where('expiry_date', '<=', now()->addMonths(3))
            ->where('quantity_remaining', '>', 0)
            ->get();

        $recentAdministrations = VaccineAdministration::with(['vaccine', 'batch', 'patient', 'administeredBy'])
            ->latest('administered_at')
            ->limit(10)
            ->get();

        return view('admin.vaccines.index', compact('vaccines', 'lowStockVaccines', 'nearExpiryBatches', 'recentAdministrations'));
    }

    public function create()
    {
        return view('admin.vaccines.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:vaccines,name',
            'manufacturer' => 'nullable|string',
            'storage_temp_range' => 'nullable|string',
            'min_stock_level' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        Vaccine::create($data);

        return redirect()->route('admin.vaccines.index')->with('success', 'Vaccine type registered.');
    }

    public function edit(Vaccine $vaccine)
    {
        return view('admin.vaccines.edit', compact('vaccine'));
    }

    public function show(Vaccine $vaccine)
    {
        $vaccine->load(['batches' => function($q) {
            $q->orderBy('expiry_date', 'asc');
        }, 'administrations' => function($q) {
            $q->latest('administered_at')->limit(50);
        }]);

        return view('admin.vaccines.show', compact('vaccine'));
    }

    public function update(Request $request, Vaccine $vaccine)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:vaccines,name,' . $vaccine->id,
            'manufacturer' => 'nullable|string',
            'storage_temp_range' => 'nullable|string',
            'min_stock_level' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $vaccine->update($data);

        return redirect()->route('admin.vaccines.index')->with('success', 'Vaccine updated.');
    }

    public function destroy(Vaccine $vaccine)
    {
        // Check if vaccine has been administered to any patient
        if ($vaccine->administrations()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete vaccine that has already been administered to patients.');
        }

        // Batches will be cascade deleted due to database constraint, 
        // but we can also check for remaining stock if we want to be extra safe
        $vaccine->delete();

        return redirect()->route('admin.vaccines.index')->with('success', 'Vaccine type deleted successfully.');
    }

    public function stockIn(Request $request)
    {
        $request->validate([
            'vaccine_id' => 'required|exists:vaccines,id',
            'batch_number' => 'required|string',
            'expiry_date' => 'required|date|after:today',
            'quantity_received' => 'required|integer|min:1',
            'received_at' => 'required|date',
            'supplier' => 'nullable|string', // RHU/MHO/Other
        ]);

        VaccineBatch::create([
            'vaccine_id' => $request->vaccine_id,
            'batch_number' => $request->batch_number,
            'expiry_date' => $request->expiry_date,
            'quantity_received' => $request->quantity_received,
            'quantity_remaining' => $request->quantity_received,
            'received_at' => $request->received_at,
            'supplier' => $request->supplier,
        ]);

        return redirect()->back()->with('success', 'New stock added to inventory.');
    }
}
