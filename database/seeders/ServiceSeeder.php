<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            ['name' => 'Consultation', 'provider_type' => 'Both'],
            ['name' => 'Immunization', 'provider_type' => 'Midwife'],
            ['name' => 'Pre-natal', 'provider_type' => 'Midwife'],
            ['name' => 'Family Planning', 'provider_type' => 'Midwife'],
            ['name' => 'Health Education', 'provider_type' => 'Both'],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(['name' => $service['name']], $service);
        }
    }
}
