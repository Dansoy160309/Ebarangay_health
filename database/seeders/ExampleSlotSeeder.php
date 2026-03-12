<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExampleSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = \App\Models\User::where('role', 'doctor')->get();
        $midwives = \App\Models\User::where('role', 'midwife')->get();
        $services = \App\Models\Service::all();

        if ($services->isEmpty()) {
             return;
        }

        $today = \Carbon\Carbon::today();

        for ($i = 0; $i < 15; $i++) {
            $service = $services->random();
            
            // Choose provider based on service type
            $provider = null;
            if ($service->provider_type === 'Doctor') {
                $provider = $doctors->random();
            } elseif ($service->provider_type === 'Midwife') {
                $provider = $midwives->random();
            } else {
                // 'Both' or fallback
                $provider = collect([$doctors, $midwives])->flatten()->random();
            }

            if (!$provider) continue;

            $date = $today->copy()->addDays(rand(1, 14)); // Future slots
            $startTime = \Carbon\Carbon::createFromTime(rand(8, 11), 0, 0); // 8 AM to 11 AM start
            $endTime = $startTime->copy()->addHours(rand(2, 4)); // 2 to 4 hour duration

            \App\Models\Slot::create([
                'service' => $service->name,
                'doctor_id' => $provider->id,
                'date' => $date->toDateString(),
                'start_time' => $startTime->format('H:i'),
                'end_time' => $endTime->format('H:i'),
                'capacity' => rand(5, 15),
                'available_spots' => rand(5, 15),
                'is_active' => true,
            ]);
        }
    }
}
