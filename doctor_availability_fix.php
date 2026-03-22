<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$rows = DB::table('doctor_availabilities')->where('is_recurring', 1)->get();
foreach ($rows as $a) {
    $dateDay = Carbon::parse($a->date)->dayOfWeek;
    $stored = is_null($a->recurring_day) ? null : (int)$a->recurring_day;
    if ($stored !== $dateDay) {
        DB::table('doctor_availabilities')->where('id', $a->id)->update(['recurring_day' => $dateDay]);
        echo "fixed id={$a->id} from={$a->recurring_day} to={$dateDay}\n";
    }
}
