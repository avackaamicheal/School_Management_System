<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageThread;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MessageController extends Controller
{
    // 1. List Inbox and specific thread
    public function index(Request $request, $school, $threadId = null)
    {
        $userId = Auth::id();

        // Get all threads this user belongs to, ordered by the latest message.
        // Grouped where clause: school scope applies to both branches.
        $threads = MessageThread::with([
            'userOne',
            'userTwo',
            'messages' => function ($q) {
                $q->latest()->limit(1);
            }
        ])
            ->where(function ($q) use ($userId) {
                $q->where('user_one_id', $userId)
                    ->orWhere('user_two_id', $userId);
            })
            ->get()
            ->sortByDesc(function ($thread) {
                return $thread->messages->first()->created_at ?? $thread->created_at;
            });

        $activeThread = null;

        if ($threadId) {
            $activeThread = MessageThread::with('messages.sender')->findOrFail($threadId);

            $this->authorize('view', $activeThread);

            // Mark all unread messages from the OTHER user as read
            Message::where('message_thread_id', $activeThread->id)
                ->where('sender_id', '!=', $userId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return view('communication.messages.index', compact('threads', 'activeThread'));
    }

    // 2. Send a Message
    public function store(Request $request, $school, MessageThread $thread)
    {
        $this->authorize('reply', $thread);

        $request->validate([
            'body' => 'required_without:attachment|string|nullable|max:5000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        Message::create([
            'message_thread_id' => $thread->id,
            'sender_id' => Auth::id(),
            'body' => $request->body,
            'attachment_path' => $attachmentPath,
        ]);

        // Touch the thread so it moves to the top of the inbox
        $thread->touch();

        return back();
    }

    // In MessageController
    public function createThread(Request $request, $school)
    {
        $request->validate(['user_id' => ['required', Rule::exists('users', 'id')->where('school_id', session('active_school'))]]);

        $userId = Auth::id();
        $targetId = $request->user_id;

        if ((int) $targetId === (int) $userId) {
            return back()->with('error', 'You cannot start a conversation with yourself.');
        }

        $target = User::whereKey($targetId)->firstOrFail();
        if ((int) $target->school_id !== (int) session('active_school')) {
            abort(403, 'You can only message users in your school.');
        }

        // Check if thread already exists
        $existing = MessageThread::where(function ($q) use ($userId, $targetId) {
            $q->where('user_one_id', $userId)->where('user_two_id', $targetId);
        })->orWhere(function ($q) use ($userId, $targetId) {
            $q->where('user_one_id', $targetId)->where('user_two_id', $userId);
        })->first();

        if ($existing) {
            return redirect(resolveRoute('messages.show', $existing->id));
        }

        $thread = MessageThread::create([
            'user_one_id' => $userId,
            'user_two_id' => $targetId,
        ]);

        return redirect(resolveRoute('messages.show', $thread->id));
    }
}
