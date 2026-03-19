<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsLog;
use App\Models\SmsSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsSettingController extends Controller
{
    public function index()
    {
        $settings = SmsSetting::all()->pluck('value', 'key');
        $logs = SmsLog::with('user')->latest()->paginate(20);
        
        // Try to fetch balance from PhilSMS if possible
        $balance = 'N/A';
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.philsms.api_token'),
                'Accept' => 'application/json',
            ])->get('https://dashboard.philsms.com/api/v3/balance');
            
            if ($response->successful()) {
                $data = $response->json();
                $balance = $data['data']['balance'] ?? 'N/A';
            }
        } catch (\Exception $e) {
            Log::error('PhilSMS Balance Error: ' . $e->getMessage());
        }

        return view('admin.sms.index', compact('settings', 'logs', 'balance'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'sms_enabled' => 'nullable|string',
            'sms_appointment_booked' => 'nullable|string',
            'sms_appointment_confirmed' => 'nullable|string',
            'sms_appointment_reminders' => 'nullable|string',
            'sms_appointment_cancelled' => 'nullable|string',
            'sms_defaulter_recall' => 'nullable|string',
        ]);

        $keys = [
            'sms_enabled', 
            'sms_appointment_booked', 
            'sms_appointment_confirmed', 
            'sms_appointment_reminders', 
            'sms_appointment_cancelled',
            'sms_defaulter_recall'
        ];

        foreach ($keys as $key) {
            SmsSetting::updateOrCreate(
                ['key' => $key],
                ['value' => isset($data[$key]) ? '1' : '0']
            );
        }

        return redirect()->back()->with('success', 'SMS settings updated successfully.');
    }

    public function broadcast(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:160',
            'target' => 'required|in:all,health_workers,patients',
        ]);

        $query = User::whereNotNull('contact_no')->where('sms_notifications', true);

        if ($request->target === 'health_workers') {
            $query->whereIn('role', ['health_worker', 'midwife', 'doctor']);
        } elseif ($request->target === 'patients') {
            $query->where('role', 'patient');
        }

        $users = $query->get();
        $count = 0;

        foreach ($users as $user) {
            // We use the PhilSmsChannel logic directly for broadcast
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('services.philsms.api_token'),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])->post(config('services.philsms.api_url'), [
                    'recipient' => $user->contact_no,
                    'sender_id' => config('services.philsms.sender_id', 'PhilSMS'),
                    'message' => $request->message,
                ]);

                SmsLog::create([
                    'user_id' => $user->id,
                    'recipient' => $user->contact_no,
                    'message' => $request->message,
                    'status' => $response->successful() ? 'sent' : 'failed',
                    'gateway_response' => $response->body(),
                ]);

                if ($response->successful()) $count++;

            } catch (\Exception $e) {
                Log::error('PhilSMS Broadcast Error: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', "Broadcast finished. {$count} messages sent successfully.");
    }

    public function clearLogs()
    {
        SmsLog::truncate();
        return redirect()->back()->with('success', 'SMS logs cleared.');
    }
}
