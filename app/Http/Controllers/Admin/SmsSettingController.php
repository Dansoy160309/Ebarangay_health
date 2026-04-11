<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsLog;
use App\Models\SmsSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SmsSettingController extends Controller
{
    public function index()
    {
        $settings = SmsSetting::all()->pluck('value', 'key');
        $logs = SmsLog::with('user')->latest()->paginate(20);
        
        // Try to fetch balance from PhilSMS if possible (with caching to avoid slow loads)
        $balance = Cache::remember('philsms_balance', 600, function () { // Cache for 10 minutes
            try {
                $response = Http::timeout(5)->withHeaders([ // 5 second timeout
                    'Authorization' => 'Bearer ' . config('services.philsms.api_token'),
                    'Accept' => 'application/json',
                ])->get('https://dashboard.philsms.com/api/v3/balance');
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['data']['remaining_balance'] ?? $data['data']['balance'] ?? 'N/A';
                }
            } catch (\Exception $e) {
                Log::error('PhilSMS Balance Error: ' . $e->getMessage());
            }
            return 'N/A';
        });

        return view('admin.sms.index', compact('settings', 'logs', 'balance'));
    }

    public function update(Request $request)
    {
        $keys = [
            'sms_enabled', 
            'sms_appointment_booked', 
            'sms_appointment_confirmed', 
            'sms_appointment_reminders', 
            'sms_appointment_cancelled',
            'sms_defaulter_recall',
            'sms_auto_defaulter_first_reminder',
        ];

        // Process each setting key
        foreach ($keys as $key) {
            // Check if the checkbox was submitted and has a truthy value
            $isEnabled = $request->has($key) && $request->input($key) === '1';
            
            SmsSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $isEnabled ? '1' : '0']
            );
        }

        // Clear any relevant caches
        Cache::forget('sms_settings');

        return redirect()->back()->with('success', 'SMS settings updated successfully.');
    }

    public function broadcast(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:160',
            'target' => 'required|in:all,health_workers,patients',
        ]);

        // Check if SMS is globally enabled
        if (!SmsSetting::isEnabled('sms_enabled')) {
            return redirect()->back()->with('error', 'SMS is currently disabled. Please enable it first.');
        }

        $query = User::whereNotNull('contact_no')->where('sms_notifications', true);

        if ($request->target === 'health_workers') {
            $query->whereIn('role', ['health_worker', 'midwife', 'doctor']);
        } elseif ($request->target === 'patients') {
            $query->where('role', 'patient');
        }

        $users = $query->get();
        $count = 0;
        $failed = 0;

        foreach ($users as $user) {
            try {
                // Format phone number for PhilSMS (add +63 prefix and remove 0 if present)
                $phoneNumber = $user->contact_no;
                // Remove any non-numeric characters except leading +
                $phoneNumber = preg_replace('/[^\d\+]/', '', $phoneNumber);
                // If starts with 0, replace with +63
                if (substr($phoneNumber, 0, 1) === '0') {
                    $phoneNumber = '+63' . substr($phoneNumber, 1);
                }
                // If doesn't start with +, add +63
                elseif (substr($phoneNumber, 0, 1) !== '+') {
                    $phoneNumber = '+63' . $phoneNumber;
                }

                $response = Http::timeout(10)->withHeaders([
                    'Authorization' => 'Bearer ' . config('services.philsms.api_token'),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])->post(config('services.philsms.api_url'), [
                    'recipient' => $phoneNumber,
                    'sender_id' => config('services.philsms.sender_id', 'PhilSMS'),
                    'message' => $request->message,
                ]);

                $isSuccess = $response->successful();
                
                SmsLog::create([
                    'user_id' => $user->id,
                    'recipient' => $user->contact_no,
                    'message' => $request->message,
                    'status' => $isSuccess ? 'sent' : 'failed',
                    'gateway_response' => $response->body(),
                ]);

                if ($isSuccess) {
                    $count++;
                } else {
                    $failed++;
                    Log::warning("SMS send failed for {$phoneNumber}. Status: {$response->status()}. Response: {$response->body()}");
                }

            } catch (\Exception $e) {
                $failed++;
                Log::error("SMS Broadcast Exception for {$user->contact_no}: {$e->getMessage()}");
                
                // Still log the failed attempt
                SmsLog::create([
                    'user_id' => $user->id,
                    'recipient' => $user->contact_no,
                    'message' => $request->message,
                    'status' => 'failed',
                    'gateway_response' => $e->getMessage(),
                ]);
            }
        }

        $message = "Broadcast finished. {$count} sent successfully";
        if ($failed > 0) {
            $message .= ", {$failed} failed.";
        }

        return redirect()->back()->with($count > 0 ? 'success' : 'error', $message);
    }

    public function clearLogs()
    {
        SmsLog::truncate();
        return redirect()->back()->with('success', 'SMS logs cleared.');
    }
}
