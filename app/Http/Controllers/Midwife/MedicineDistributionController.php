<?php

namespace App\Http\Controllers\Midwife;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineDistribution;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicineDistributionController extends Controller
{
    public function create()
    {
        $patients = Patient::with('guardian')->orderBy('last_name')->get();
        $medicines = Medicine::orderBy('generic_name')->get();

        return view('midwife.medicines.distribute', compact('patients', 'medicines'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'patient_id' => 'required|exists:users,id',
                'medicine_id' => 'required|exists:medicines,id',
                'quantity' => 'required|integer|min:1',
                'dosage' => 'required|string|max:255',
                'frequency' => 'required|string|max:255',
                'duration' => 'required|string|max:255',
                'notes' => 'nullable|string',
            ],
            [],
            [
                'patient_id' => 'patient',
                'medicine_id' => 'medicine',
            ]
        );

        DB::transaction(function () use ($data) {
            $medicine = Medicine::lockForUpdate()->findOrFail($data['medicine_id']);

            if ($medicine->stock < $data['quantity']) {
                abort(422, 'Insufficient stock for this medicine.');
            }

            $medicine->decrement('stock', $data['quantity']);

            MedicineDistribution::create([
                'medicine_id' => $medicine->id,
                'patient_id' => $data['patient_id'],
                'midwife_id' => auth()->id(),
                'quantity' => $data['quantity'],
                'dosage' => $data['dosage'],
                'frequency' => $data['frequency'],
                'duration' => $data['duration'],
                'notes' => $data['notes'] ?? null,
            ]);
        });

        return redirect()->route('midwife.medicines.distribute.create')
            ->with('success', 'Medicine distributed and inventory updated.');
    }
}
