<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Central tenant check: target must belong to active school,
     * and actor must be operating inside that school.
     */
    protected function sameSchool(User $actor, User $target): bool
    {
        if (session()->has('active_school') && session('active_school')) {
            if ((int) $target->school_id !== (int) session('active_school')) {
                return false;
            }
            // Actor must also belong to the active school (except SuperAdmin).
            if (! $actor->hasRole('SuperAdmin') && (int) $actor->school_id !== (int) session('active_school')) {
                return false;
            }
        } else {
            // No tenant context: only SuperAdmin may act cross-school.
            if (! $actor->hasRole('SuperAdmin')) {
                return false;
            }
        }

        return true;
    }

    protected function isSchoolAdmin(User $actor): bool
    {
        return $actor->hasRole('SchoolAdmin') || $actor->hasRole('SuperAdmin');
    }

    public function viewTeacher(User $user, User $teacher): bool
    {
        if (! $this->sameSchool($user, $teacher)) {
            return false;
        }

        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        return $user->hasRole('SchoolAdmin');
    }

    public function updateTeacher(User $user, User $teacher): bool
    {
        return $this->viewTeacher($user, $teacher);
    }

    public function deleteTeacher(User $user, User $teacher): bool
    {
        if (! $this->sameSchool($user, $teacher)) {
            return false;
        }

        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        // Only SchoolAdmin can delete a teacher.
        return $user->hasRole('SchoolAdmin');
    }

    public function viewStudent(User $user, User $student): bool
    {
        if (! $this->sameSchool($user, $student)) {
            return false;
        }

        if ($user->hasRole('SuperAdmin') || $user->hasRole('SchoolAdmin')) {
            return true;
        }

        // Teachers may view students they teach (checked in controller via section);
        // policy grants tenant-level view, controller adds section check where needed.
        if ($user->hasRole('Teacher')) {
            return true;
        }

        if ($user->hasRole('Parent')) {
            return $user->children()->where('children.id', $student->id)->exists();
        }

        if ($user->hasRole('Student')) {
            return (int) $user->id === (int) $student->id;
        }

        return false;
    }

    public function deleteStudent(User $user, User $student): bool
    {
        if (! $this->sameSchool($user, $student)) {
            return false;
        }

        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        return $user->hasRole('SchoolAdmin');
    }

    public function viewParent(User $user, User $parent): bool
    {
        if (! $this->sameSchool($user, $parent)) {
            return false;
        }

        if ($user->hasRole('SuperAdmin') || $user->hasRole('SchoolAdmin')) {
            return true;
        }

        // Parent viewing self.
        if ($user->hasRole('Parent') && (int) $user->id === (int) $parent->id) {
            return true;
        }

        return false;
    }

    public function updateParent(User $user, User $parent): bool
    {
        if (! $this->sameSchool($user, $parent)) {
            return false;
        }

        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        if ($user->hasRole('SchoolAdmin')) {
            return true;
        }

        // Parent may update own profile.
        if ($user->hasRole('Parent') && (int) $user->id === (int) $parent->id) {
            return true;
        }

        return false;
    }
}
