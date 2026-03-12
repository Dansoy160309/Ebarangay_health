<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExampleAnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminId = \App\Models\User::where('role', 'admin')->first()->id ?? 1;

        $announcements = [
            [
                'title' => 'Vaccination Schedule for October',
                'message' => 'The Barangay Health Center will conduct a mass vaccination program for children aged 0-5 years on October 10-15, 2026. Please bring your child\'s immunization record.',
                'created_by' => $adminId,
                'status' => 'active',
                'published_at' => now(),
                'expires_at' => now()->addDays(15),
            ],
            [
                'title' => 'Monthly Prenatal Check-up',
                'message' => 'All pregnant women in the barangay are invited to attend the monthly prenatal check-up this coming Friday, March 13, 2026, starting at 8:00 AM.',
                'created_by' => $adminId,
                'status' => 'active',
                'published_at' => now(),
                'expires_at' => now()->addDays(10),
            ],
            [
                'title' => 'Health Center Holiday Notice',
                'message' => 'The Health Center will be closed on March 27, 2026, in observance of a local holiday. Normal operations will resume on March 30, 2026.',
                'created_by' => $adminId,
                'status' => 'active',
                'published_at' => now(),
                'expires_at' => now()->addDays(25),
            ],
            [
                'title' => 'Free Medicine Distribution',
                'message' => 'Free vitamins and basic medicines will be distributed to senior citizens on April 5, 2026. Please present your Senior Citizen ID.',
                'created_by' => $adminId,
                'status' => 'active',
                'published_at' => now(),
                'expires_at' => now()->addDays(30),
            ],
            [
                'title' => 'Dengue Awareness Seminar',
                'message' => 'Join us for a seminar on Dengue Prevention and Awareness on March 20, 2026, at the Barangay Hall. Let\'s keep our community safe!',
                'created_by' => $adminId,
                'status' => 'active',
                'published_at' => now(),
                'expires_at' => now()->addDays(15),
            ],
        ];

        foreach ($announcements as $announcement) {
            \App\Models\Announcement::create($announcement);
        }
    }
}
