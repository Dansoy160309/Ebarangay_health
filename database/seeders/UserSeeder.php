<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@barangay.com'],
            [
                'first_name' => 'Admin',
                'middle_name' => '',
                'last_name' => 'Admin',
                'dob' => '2003-03-16',
                'gender' => 'Male',
                'address' => '123 Admin St',
                'purok' => 'Purok 1',
                'contact_no' => '09363796133',
                'emergency_no' => '09987654321',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 1,
                'must_change_password' => false,
            ]
        );

        // Health Worker
        User::updateOrCreate(
            ['email' => 'health@ebarangay.com'],
            [
                'first_name' => 'Health',
                'middle_name' => '',
                'last_name' => 'Worker',
                'dob' => '1992-02-02',
                'gender' => 'Female',
                'address' => '456 Clinic St',
                'purok' => 'Purok 2',
                'contact_no' => '09123456780',
                'emergency_no' => null,
                'password' => Hash::make('healthworker123'),
                'role' => 'health_worker',
                'status' => 1,
                'must_change_password' => false,
            ]
        );

        // Doctor
        User::updateOrCreate(
            ['email' => 'doctor@barangay.com'],
            [
                'first_name' => 'Doc',
                'middle_name' => 'Tor',
                'last_name' => 'Strange',
                'dob' => '1985-05-05',
                'gender' => 'Male',
                'address' => '789 Medical St',
                'purok' => 'Purok 3',
                'contact_no' => '09123456782',
                'emergency_no' => null,
                'password' => Hash::make('doctor123'),
                'role' => 'doctor',
                'status' => 1,
                'must_change_password' => false,
            ]
        );

        // Midwife (Health Worker)
        User::updateOrCreate(
            ['email' => 'midwife@barangay.com'],
            [
                'first_name' => 'Mae',
                'middle_name' => '',
                'last_name' => 'Midwife',
                'dob' => '1990-08-15',
                'gender' => 'Female',
                'address' => '123 Maternity Care St',
                'purok' => 'Purok 4',
                'contact_no' => '09123456783',
                'emergency_no' => null,
                'password' => Hash::make('midwife123'),
                'role' => 'midwife',
                'status' => 1,
                'must_change_password' => false,
            ]
        );

        // Patient
        User::updateOrCreate(
            ['email' => 'patient@ebarangay.com'],
            [
                'first_name' => 'John',
                'middle_name' => 'M',
                'last_name' => 'Doe',
                'dob' => '2000-05-05',
                'gender' => 'Male',
                'address' => '789 Resident St',
                'purok' => 'Purok 3',
                'contact_no' => '09123456781',
                'emergency_no' => '09987654322',
                'password' => Hash::make('123456789'),
                'role' => 'patient',
                'status' => 1,
                'must_change_password' => false,
            ]
        );

        // Generic Test Patient (for demos/testing)
        User::updateOrCreate(
            ['email' => 'test@ebarangay.com'],
            [
                'first_name' => 'Test',
                'middle_name' => '',
                'last_name' => 'Patient',
                'dob' => '1995-01-01',
                'gender' => 'Male',
                'address' => 'Test Street',
                'purok' => 'Purok 5',
                'contact_no' => '09123456789',
                'emergency_no' => null,
                'password' => Hash::make('123456789'),
                'role' => 'patient',
                'status' => 1,
                'must_change_password' => false,
            ]
        );
    }
}
