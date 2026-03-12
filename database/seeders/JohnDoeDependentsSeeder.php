<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JohnDoeDependentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $john = \App\Models\User::where('first_name', 'like', '%John%')
            ->where('last_name', 'like', '%Doe%')
            ->first();

        if (!$john) {
            return;
        }

        // Dependent 1: Child (Infant for Immunization)
        \App\Models\User::updateOrCreate(
            ['first_name' => 'Baby', 'last_name' => 'Doe', 'guardian_id' => $john->id],
            [
                'middle_name' => 'Santos',
                'dob' => now()->subMonths(6)->toDateString(),
                'gender' => 'Male',
                'address' => $john->address,
                'purok' => $john->purok,
                'contact_no' => $john->contact_no,
                'email' => 'baby.doe@example.com',
                'password' => 'password',
                'role' => 'patient',
                'relationship' => 'Son',
                'dependency_reason' => 'Minor',
                'status' => true,
            ]
        );

        // Dependent 2: Elderly Parent (for Consultation/Maintenance)
        \App\Models\User::updateOrCreate(
            ['first_name' => 'Senior', 'last_name' => 'Doe', 'guardian_id' => $john->id],
            [
                'middle_name' => 'Garcia',
                'dob' => now()->subYears(70)->toDateString(),
                'gender' => 'Female',
                'address' => $john->address,
                'purok' => $john->purok,
                'contact_no' => $john->contact_no,
                'email' => 'senior.doe@example.com',
                'password' => 'password',
                'role' => 'patient',
                'relationship' => 'Mother',
                'dependency_reason' => 'Elderly',
                'status' => true,
            ]
        );
    }
}
