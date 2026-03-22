<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements for the doctor.
     */
    public function index()
    {
        $announcements = Announcement::where('status', 'active')
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->paginate(10);

        return view('doctor.announcements.index', compact('announcements'));
    }

    /**
     * Display the specified announcement.
     */
    public function show(Announcement $announcement)
    {
        // Ensure the announcement is active/viewable
        if ($announcement->status !== 'active' || ($announcement->expires_at && $announcement->expires_at->isPast())) {
            abort(404);
        }

        // Mark related announcement notification(s) as read so nav badges clear
        Auth::user()->unreadNotifications
            ->where('data.announcement_id', $announcement->id)
            ->each(fn($notification) => $notification->markAsRead());

        return view('doctor.announcements.show', compact('announcement'));
    }
}
