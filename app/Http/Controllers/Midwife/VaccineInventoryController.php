<?php

namespace App\Http\Controllers\Midwife;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vaccine;
use App\Models\VaccineBatch;

class VaccineInventoryController extends Controller
{
    public function index()
    {
        $vaccines = Vaccine::with(['batches' => function($q) {
            $q->orderBy('expiry_date', 'asc');
        }])->get();

        $nearExpiryBatches = VaccineBatch::with('vaccine')
            ->where('expiry_date', '<=', now()->addMonths(3))
            ->where('quantity_remaining', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->get();

        $lowStockVaccines = $vaccines->filter(fn($v) => $v->in_stock_quantity <= $v->min_stock_level);

        return view('midwife.inventory.index', compact('vaccines', 'nearExpiryBatches', 'lowStockVaccines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vaccine_id' => 'required|exists:vaccines,id',
            'batch_number' => 'required|string',
            'expiry_date' => 'required|date|after:today',
            'quantity_received' => 'required|integer|min:1',
            'received_at' => 'required|date',
            'supplier' => 'nullable|string',
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

        return redirect()->back()->with('success', 'Vaccine batch added successfully.');
    }

    public function storeVaccine(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:vaccines,name',
            'manufacturer' => 'nullable|string',
            'storage_temp_range' => 'nullable|string',
            'min_stock_level' => 'required|integer|min:0',
        ]);

        Vaccine::create($request->all());

        return redirect()->back()->with('success', 'New vaccine type registered.');
    }
}
