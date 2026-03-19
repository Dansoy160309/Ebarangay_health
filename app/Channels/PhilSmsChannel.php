<?php

namespace App\Channels;

use App\Models\SmsLog;
use App\Models\SmsSetting;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhilSmsChannel
{
    public function send($notifiable, Notification $notification)
    {
        /** @var mixed $notification */
        
        // 1. Check Global Toggle
        if (!SmsSetting::isEnabled('sms_enabled')) {
            return;
        }

        // 2. Check Patient Preference (if notifiable is a user)
        if (isset($notifiable->sms_notifications) && !$notifiable->sms_notifications) {
            return;
        }

        if (!method_exists($notification, 'toSms')) {
            return;
        }

        $messageData = $notification->toSms($notifiable);

        if (empty($messageData) || !isset($messageData['recipient']) || !isset($messageData['body'])) {
            return;
        }

        // 3. Check Specific Service Toggle
        $smsType = property_exists($notification, 'smsType') ? $notification->smsType : null;
        if ($smsType && is_string($smsType) && !SmsSetting::isEnabled($smsType)) {
            return;
        }

        $recipient = $messageData['recipient'];
        $body = $messageData['body'];

        try {
            $apiUrl = config('services.philsms.api_url', 'https://dashboard.philsms.com/api/v3/sms/send');
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.philsms.api_token'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($apiUrl, [
                'recipient' => $recipient,
                'sender_id' => config('services.philsms.sender_id', 'PhilSMS'),
                'message' => $body,
            ]);

            // 4. Log the attempt
            SmsLog::create([
                'user_id' => isset($notifiable->id) ? $notifiable->id : null,
                'recipient' => $recipient,
                'message' => $body,
                'status' => $response->successful() ? 'sent' : 'failed',
                'gateway_response' => $response->body(),
            ]);

            if ($response->failed()) {
                Log::error('PhilSMS Error: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('PhilSMS Exception: ' . $e->getMessage());
            
            // Log the exception
            SmsLog::create([
                'user_id' => isset($notifiable->id) ? $notifiable->id : null,
                'recipient' => $recipient,
                'message' => $body,
                'status' => 'failed',
                'gateway_response' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }
}
