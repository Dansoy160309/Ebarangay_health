<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;
use App\Models\User;

try {
    $user = User::first();
    if (!$user) {
        die("No user found in database.\n");
    }
    
    echo "Attempting to send test email to {$user->email}...\n";
    
    Mail::to($user->email)->send(new PasswordResetMail($user, 'https://example.com/reset-test'));
    
    echo "SUCCESS: Email sent successfully according to Laravel!\n";
} catch (\Exception $e) {
    echo "FAILURE: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
