<?php

namespace App\Http\Controllers\HealthWorker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\User;
use App\Models\Patient;
use App\Notifications\NewAnnouncementNotification;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->paginate(10);
        return view('healthworker.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('healthworker.announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'status' => 'required|in:active,archived',
            'expires_at' => 'nullable|date|after_or_equal:today',
        ]);

        $announcement = Announcement::create([
            'title' => $request->title,
            'message' => $request->message,
            'status' => $request->status,
            'expires_at' => $request->expires_at,
            'published_at' => $request->status === 'active' ? now() : null,
            'created_by' => auth()->id(),
        ]);

        if ($announcement->status === 'active') {
            User::where('status', true)
                ->where('id', '!=', auth()->id())
                ->chunkById(100, function ($users) use ($announcement) {
                    foreach ($users as $user) {
                        $user->notify(new NewAnnouncementNotification($announcement));
                    }
                });
        }

        return redirect()->route('healthworker.announcements.index')->with('success', 'Announcement created successfully.');
    }

    public function show($id)
    {
        $announcement = Announcement::findOrFail($id);
        return view('healthworker.announcements.show', compact('announcement'));
    }
}
