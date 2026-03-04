<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\School;
use App\Models\User;
use App\Notifications\AnnouncementPosted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class AnnouncementController extends Controller
{
    public function index(Request $request, School $school)
    {
        $announcements = Announcement::with('author')
            ->where('publish_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->latest()
            ->get();
        return view('communication.announcements.index', compact('announcements'));
    }

    public function store(Request $request, School $school)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_role' => 'nullable|string',
            'publish_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:publish_at',
        ]);

        $announcement = Announcement::create([
            'author_id' => Auth::id(),
            'title' => $request->title,
            'content'     => $request->content,
            'target_role' => $request->target_role,
            'publish_at' => $request->publish_at ?? now(),
            'expires_at' => $request->expires_at,
        ]);

        // If it's scheduled to publish immediately, send the notifications now
        if ($announcement->publish_at <= now()) {
            $this->broadcastNotification($announcement);
        }

        return back()->with('success', 'Announcement posted successfully!');
    }

    public function destroy(Request $request, $school, Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Announcement deleted.');
    }

    // Helper method to target the correct users
    private function broadcastNotification($announcement)
    {
        $users = User::query();

        // If a specific role is targeted, filter by that role
        if ($announcement->target_role) {
            $users->role($announcement->target_role);
        }

        // Send the database notification to the targeted users
        Notification::send($users->get(), new AnnouncementPosted($announcement));
    }
}
