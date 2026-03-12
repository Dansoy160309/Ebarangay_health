<?php

use App\Models\Appointment;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$appointment = Appointment::find(1);
if ($appointment) {
    $appointment->scheduled_at = '2026-03-09 08:00:00';
    $appointment->status = 'approved';
    $appointment->save();
    echo "Appointment #1 updated successfully to March 09, 2026 08:00 AM and status: approved\n";
} else {
    echo "Appointment #1 not found\n";
}
