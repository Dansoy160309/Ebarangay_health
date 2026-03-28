<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Slot;
use Carbon\Carbon;

echo "Current Server Time: " . now()->toDateTimeString() . "\n";
echo "Today Date: " . today()->toDateString() . "\n";

$slots = Slot::whereDate('date', '2026-03-28')
    ->withCount(['appointments' => function($q) {
        $q->whereNotIn('status', ['cancelled', 'rejected']);
    }])
    ->get();

foreach ($slots as $slot) {
    echo "ID: " . $slot->id . "\n";
    echo "Service: " . $slot->service . "\n";
    echo "Capacity: " . $slot->capacity . "\n";
    echo "Appointments Count: " . $slot->appointments_count . "\n";
    echo "Available Spots (DB column): " . $slot->available_spots . "\n";
    echo "Remaining Seats (Method): " . $slot->remainingSeats() . "\n";
    echo "Is Expired: " . ($slot->isExpired() ? 'Yes' : 'No') . "\n";
    echo "Is Active: " . ($slot->is_active ? 'Yes' : 'No') . "\n";
    echo "Start Time: " . $slot->start_time->format('H:i:s') . "\n";
    echo "End Time: " . $slot->end_time->format('H:i:s') . "\n";
    echo "--------------------\n";
}
