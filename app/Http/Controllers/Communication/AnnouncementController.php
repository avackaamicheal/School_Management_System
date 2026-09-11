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
        $user = Auth::user();

        $announcements = Announcement::with('author')
            ->where('school_id', session('active_school'))
            ->where('publish_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            // target_role filtering enforced server-side; cannot be bypassed by URL.
            ->where(function ($query) use ($user) {
                $query->whereNull('target_role')
                    ->orWhere('target_role', '');
                foreach (['Student', 'Teacher', 'Parent', 'SchoolAdmin', 'Bursar'] as $role) {
                    if ($user->hasRole($role)) {
                        $query->orWhere('target_role', $role);
                    }
                }
                if ($user->hasRole('SuperAdmin')) {
                    $query->orWhereNotNull('target_role');
                }
            })
            ->latest()
            ->get();
        return view('communication.announcements.index', compact('announcements'));
    }

    public function store(Request $request, School $school)
    {
        $this->authorize('create', Announcement::class);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:10000',
            'target_role' => 'nullable|in:Student,Teacher,Parent,SchoolAdmin,Bursar',
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
    {
        $this->authorize('delete', $announcement);

        $announcement->delete();
        return back()->with('success', 'Announcement deleted.');
    }
}
