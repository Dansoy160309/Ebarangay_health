<?php

namespace App\Http\Controllers\Admin;

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
        $activeAnnouncements = Announcement::where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->paginate(9, ['*'], 'active_page');

        $archivedAnnouncements = Announcement::where(function ($query) {
                $query->where('status', 'archived')
                    ->orWhere(function ($expiredQuery) {
                        $expiredQuery->where('status', 'active')
                            ->whereNotNull('expires_at')
                            ->where('expires_at', '<=', now());
                    });
            })
            ->latest()
            ->paginate(6, ['*'], 'archive_page');

        return view('admin.announcements.index', compact('activeAnnouncements', 'archivedAnnouncements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
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

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement created successfully.');
    }

    public function show($id)
    {
        $announcement = Announcement::findOrFail($id);
        return view('admin.announcements.show', compact('announcement'));
    }

    public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'status' => 'required|in:active,archived',
            'expires_at' => 'nullable|date|after_or_equal:today',
        ]);

        $wasArchived = $announcement->status === 'archived';

        $announcement->update([
            'title' => $request->title,
            'message' => $request->message,
            'status' => $request->status,
            'expires_at' => $request->expires_at,
        ]);
        
        if ($request->status === 'active' && !$announcement->published_at) {
            $announcement->update(['published_at' => now()]);
        }

        if ($request->status === 'active' && $wasArchived) {
            User::where('status', true)
                ->where('id', '!=', auth()->id())
                ->chunkById(100, function ($users) use ($announcement) {
                    foreach ($users as $user) {
                        $user->notify(new NewAnnouncementNotification($announcement));
                    }
                });
        }

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated successfully.');
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();
        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted successfully.');
    }
}
