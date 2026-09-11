<?php

namespace App\Policies;

use App\Models\GradeRecord;
use App\Models\User;

class GradeRecordPolicy
{
    /**
     * Teacher may only view/edit grades for sections they're assigned to
     * (via ClassroomAssignment). Student/Parent may only view own/child grades.
     */
    public function view(User $user, GradeRecord $grade): bool
    {
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        if ($user->hasRole('SchoolAdmin')) {
            return (int) $user->school_id === (int) $grade->school_id;
        }

        if ($user->hasRole('Teacher')) {
            return $user->allocations()
                ->where('section_id', $grade->section_id)
                ->where('subject_id', $grade->subject_id)
                ->exists();
        }

        if ($user->hasRole('Student')) {
            return (int) $user->id === (int) $grade->student_id;
        }

        if ($user->hasRole('Parent')) {
            return $user->children()->where('users.id', $grade->student_id)->exists()
                || $user->children()->where('student_id', $grade->student_id)->exists();
        }

        return false;
    }

    public function update(User $user, GradeRecord $grade): bool
    {
        if ($grade->is_locked) {
            return false;
        }

        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        if ($user->hasRole('SchoolAdmin')) {
            return (int) $user->school_id === (int) $grade->school_id;
        }

        if ($user->hasRole('Teacher')) {
            return $user->allocations()
                ->where('section_id', $grade->section_id)
                ->where('subject_id', $grade->subject_id)
                ->exists();
        }

        return false;
    }
}
