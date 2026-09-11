<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    public function view(User $user, Announcement $announcement): bool
    {
        if (session()->has('active_school') && session('active_school')) {
            if ((int) $announcement->school_id !== (int) session('active_school')) {
                return false;
            }
        }

        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        // target_role filtering cannot be bypassed: null = all roles.
        if (empty($announcement->target_role)) {
            return true;
        }

        return $user->hasRole($announcement->target_role);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('SuperAdmin')
            || $user->hasRole('SchoolAdmin')
            || $user->hasRole('Teacher');
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        if (session()->has('active_school') && session('active_school')) {
            if ((int) $announcement->school_id !== (int) session('active_school')) {
                return false;
            }
        }

        if ($user->hasRole('SuperAdmin') || $user->hasRole('SchoolAdmin')) {
            return true;
        }

        // Teachers may delete only their own announcements.
        if ($user->hasRole('Teacher')) {
            return (int) $announcement->author_id === (int) $user->id;
        }

        return false;
    }
}
