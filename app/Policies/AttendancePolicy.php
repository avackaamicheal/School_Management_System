<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    /**
     * Teacher may only view/edit attendance for assigned sections.
     * Student/Parent may only view own/child attendance.
     */
    public function view(User $user, Attendance $attendance): bool
    {
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        if ($user->hasRole('SchoolAdmin') || $user->hasRole('Bursar')) {
            return (int) $user->school_id === (int) $attendance->school_id;
        }

        if ($user->hasRole('Teacher')) {
            return in_array((int) $attendance->section_id, $user->allowedSectionIds(), true);
        }

        if ($user->hasRole('Student')) {
            return (int) $user->id === (int) $attendance->student_id;
        }

        if ($user->hasRole('Parent')) {
            return $user->children()->where('users.id', $attendance->student_id)->exists()
                || $user->children()->where('student_id', $attendance->student_id)->exists();
        }

        return false;
    }

    public function update(User $user, Attendance $attendance): bool
    {
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        if ($user->hasRole('SchoolAdmin')) {
            return (int) $user->school_id === (int) $attendance->school_id;
        }

        if ($user->hasRole('Teacher')) {
            return in_array((int) $attendance->section_id, $user->allowedSectionIds(), true);
        }

        return false;
    }
}
