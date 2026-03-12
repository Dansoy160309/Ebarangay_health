<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\Appointment;
use App\Models\Announcement;
use App\Notifications\UpcomingAppointmentReminder;
use App\Notifications\NewAnnouncementNotification;

class NotificationLogController extends Controller
{
    public function index(Request $request)
    {
        $query = DatabaseNotification::query()->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('status')) {
            $query->where('data->status', $request->input('status'));
        }

        $notifications = $query->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function resend(string $id)
    {
        $notification = DatabaseNotification::findOrFail($id);
        $notifiable = $notification->notifiable;

        if (!$notifiable) {
            return back()->with('error', 'Original recipient no longer exists.');
        }

        $type = $notification->type;
        $data = $notification->data ?? [];

        if ($type === UpcomingAppointmentReminder::class) {
            $appointmentId = $data['appointment_id'] ?? null;
            $appointment = $appointmentId ? Appointment::find($appointmentId) : null;

            if ($appointment && $appointment->user) {
                $appointment->user->notify(new UpcomingAppointmentReminder($appointment));
                $data['status'] = 'resent';
                $notification->data = $data;
                $notification->save();

                return back()->with('success', 'Appointment reminder resent.');
            }

            return back()->with('error', 'Unable to resend: appointment not found.');
        }

        if ($type === NewAnnouncementNotification::class) {
            $announcementId = $data['announcement_id'] ?? null;
            $announcement = $announcementId ? Announcement::find($announcementId) : null;

            if ($announcement) {
                $notifiable->notify(new NewAnnouncementNotification($announcement));
                $data['status'] = 'resent';
                $notification->data = $data;
                $notification->save();

                return back()->with('success', 'Announcement notification resent.');
            }

            return back()->with('error', 'Unable to resend: announcement not found.');
        }

        return back()->with('error', 'Resend is not supported for this notification type.');
    }
}

