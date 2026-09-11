<?php

namespace App\Policies;

use App\Models\MessageThread;
use App\Models\User;

class MessagePolicy
{
    /**
     * Only a participant (one of the two parties) may view the thread.
     */
    public function view(User $user, MessageThread $thread): bool
    {
        if (session()->has('active_school') && session('active_school')) {
            if ((int) $thread->school_id !== (int) session('active_school')) {
                return false;
            }
        }

        return (int) $user->id === (int) $thread->user_one_id
            || (int) $user->id === (int) $thread->user_two_id;
    }

    /**
     * Only a participant may reply/post to the thread.
     */
    public function reply(User $user, MessageThread $thread): bool
    {
        return $this->view($user, $thread);
    }
}
