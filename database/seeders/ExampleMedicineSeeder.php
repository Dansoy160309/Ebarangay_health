<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExampleMedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicines = [
            ['generic_name' => 'Paracetamol', 'brand_name' => 'Biogesic', 'dosage_form' => 'Tablet', 'strength' => '500mg', 'stock' => 500, 'reorder_level' => 100, 'expiration_date' => '2027-12-31'],
            ['generic_name' => 'Amoxicillin', 'brand_name' => 'Amoxil', 'dosage_form' => 'Capsule', 'strength' => '500mg', 'stock' => 300, 'reorder_level' => 50, 'expiration_date' => '2026-06-30'],
            ['generic_name' => 'Cetirizine', 'brand_name' => 'Virlix', 'dosage_form' => 'Tablet', 'strength' => '10mg', 'stock' => 200, 'reorder_level' => 40, 'expiration_date' => '2027-03-15'],
            ['generic_name' => 'Salbutamol', 'brand_name' => 'Ventolin', 'dosage_form' => 'Syrup', 'strength' => '2mg/5mL', 'stock' => 100, 'reorder_level' => 20, 'expiration_date' => '2026-11-20'],
            ['generic_name' => 'Mefenamic Acid', 'brand_name' => 'Ponstan', 'dosage_form' => 'Capsule', 'strength' => '500mg', 'stock' => 400, 'reorder_level' => 80, 'expiration_date' => '2027-01-10'],
            ['generic_name' => 'Losartan', 'brand_name' => 'Cozaar', 'dosage_form' => 'Tablet', 'strength' => '50mg', 'stock' => 250, 'reorder_level' => 50, 'expiration_date' => '2028-05-22'],
            ['generic_name' => 'Metformin', 'brand_name' => 'Glucophage', 'dosage_form' => 'Tablet', 'strength' => '500mg', 'stock' => 300, 'reorder_level' => 60, 'expiration_date' => '2027-09-18'],
            ['generic_name' => 'Multivitamins', 'brand_name' => 'Enervon', 'dosage_form' => 'Tablet', 'strength' => 'Standard', 'stock' => 600, 'reorder_level' => 120, 'expiration_date' => '2026-12-25'],
            ['generic_name' => 'Oral Rehydration Salts', 'brand_name' => 'Hydrite', 'dosage_form' => 'Sachet', 'strength' => '20.5g', 'stock' => 150, 'reorder_level' => 30, 'expiration_date' => '2028-02-14'],
            ['generic_name' => 'Ascorbic Acid', 'brand_name' => 'Ceelin', 'dosage_form' => 'Syrup', 'strength' => '100mg/5mL', 'stock' => 120, 'reorder_level' => 25, 'expiration_date' => '2026-08-05'],
        ];

        foreach ($medicines as $med) {
            \App\Models\Medicine::updateOrCreate(
                ['generic_name' => $med['generic_name'], 'brand_name' => $med['brand_name']],
                $med
            );
        }
    }
}
