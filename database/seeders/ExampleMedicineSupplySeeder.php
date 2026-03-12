<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExampleMedicineSupplySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminId = \App\Models\User::where('role', 'admin')->first()->id ?? 1;

        $supplies = [
            [
                'medicine_name' => 'Paracetamol',
                'batch_number' => 'BATCH-PARA-2024-001',
                'quantity' => 100,
                'expiration_date' => '2026-12-31',
                'supplier_name' => 'Municipal Health Office (MHO)',
                'date_received' => now()->toDateString(),
            ],
            [
                'medicine_name' => 'Amoxicillin',
                'batch_number' => 'BATCH-AMOX-2024-002',
                'quantity' => 50,
                'expiration_date' => '2025-06-30',
                'supplier_name' => 'DOH Regional Warehouse',
                'date_received' => now()->toDateString(),
            ],
            [
                'medicine_name' => 'Cetirizine',
                'batch_number' => 'BATCH-CETI-2024-003',
                'quantity' => 200,
                'expiration_date' => '2027-03-15',
                'supplier_name' => 'PharmaDist Inc.',
                'date_received' => now()->toDateString(),
            ],
            [
                'medicine_name' => 'Multivitamins',
                'batch_number' => 'BATCH-MVIT-2024-004',
                'quantity' => 500,
                'expiration_date' => '2026-12-25',
                'supplier_name' => 'LGU Procurement',
                'date_received' => now()->toDateString(),
            ],
            [
                'medicine_name' => 'Ascorbic Acid',
                'batch_number' => 'BATCH-VITC-2024-005',
                'quantity' => 150,
                'expiration_date' => '2026-08-05',
                'supplier_name' => 'CureAll Supplies',
                'date_received' => now()->toDateString(),
            ],
        ];

        foreach ($supplies as $supply) {
            $medicine = \App\Models\Medicine::where('generic_name', $supply['medicine_name'])->first();
            
            if ($medicine) {
                \App\Models\MedicineSupply::create([
                    'medicine_id' => $medicine->id,
                    'batch_number' => $supply['batch_number'],
                    'quantity' => $supply['quantity'],
                    'expiration_date' => $supply['expiration_date'],
                    'supplier_name' => $supply['supplier_name'],
                    'date_received' => $supply['date_received'],
                    'received_by' => $adminId,
                ]);

                // Update the medicine's total stock
                $medicine->increment('stock', $supply['quantity']);
            }
        }
    }
}
