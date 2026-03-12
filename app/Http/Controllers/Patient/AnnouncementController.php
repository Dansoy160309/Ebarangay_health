<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements for the patient.
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

        return view('patient.announcements.index', compact('announcements'));
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

        return view('patient.announcements.show', compact('announcement'));
    }
}
