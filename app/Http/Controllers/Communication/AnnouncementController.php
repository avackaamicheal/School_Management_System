<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\School;
use App\Models\User;
use App\Notifications\AnnouncementPostedNotification;
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
            'content' => $request->content,
            'target_role' => $request->target_role,
            'publish_at' => $request->publish_at ?? now(),
            'expires_at' => $request->expires_at,
        ]);

        // Only send notifications if publishing immediately
        if ($announcement->publish_at <= now()) {
            $targetUsers = User::where('school_id', session('active_school'))
                ->when($announcement->target_role, function ($q) use ($announcement) {
                    $q->role($announcement->target_role);
                })
                ->get();

            Notification::send($targetUsers, new AnnouncementPostedNotification($announcement));
        }

        return back()->with('success', 'Announcement posted successfully!');
    }

    public function destroy(Request $request, $school, Announcement $announcement)
    {        $announcement->delete();
        return back()->with('success', 'Announcement deleted.');
    }
}
