<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$rows = DB::table('doctor_availabilities')->get();
foreach ($rows as $a) {
    echo "id={$a->id} doctor_id={$a->doctor_id} date={$a->date} recurring={$a->is_recurring} day={$a->recurring_day} status={$a->status}\n";
}
