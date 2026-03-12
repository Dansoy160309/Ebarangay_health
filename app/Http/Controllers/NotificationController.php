<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // List all notifications
    public function index()
    {
        $notifications = Auth::user()->notifications()->latest()->get();
        return view('notifications.index', compact('notifications'));
    }

    // Mark a single notification as read
    public function read($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        // Redirect based on notification type
        if (isset($notification->data['announcement_id'])) {
            $role = Auth::user()->role;
            $prefix = match($role) {
                'admin' => 'admin',
                'health_worker' => 'healthworker',
                'doctor' => 'doctor',
                'midwife' => 'midwife',
                default => 'patient',
            };
            return redirect()->route($prefix . '.announcements.show', $notification->data['announcement_id']);
        }

        if (isset($notification->data['appointment_id'])) {
            $role = Auth::user()->role;
            $prefix = match($role) {
                'admin' => 'admin',
                'health_worker' => 'healthworker',
                'doctor' => 'doctor',
                'midwife' => 'midwife',
                default => 'patient',
            };
            return redirect()->route($prefix . '.appointments.show', $notification->data['appointment_id']);
        }

        return redirect()->back();
    }

    // Mark all notifications as read
    public function markAll()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->back();
    }
}
