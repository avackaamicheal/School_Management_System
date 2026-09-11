<?php

namespace App\Policies;

use App\Models\School;
use App\Models\User;

class SchoolPolicy
{
    /**
     * All school management actions restricted to SuperAdmin only,
     * formalizing existing SchoolController route middleware.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('SuperAdmin');
    }

    public function view(User $user, School $school): bool
    {
        // SuperAdmin global view; SchoolAdmin may view own school profile
        // via the dedicated school-profile route (handled separately).
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        if ($user->hasRole('SchoolAdmin') && (int) $user->school_id === (int) $school->id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('SuperAdmin');
    }

    public function update(User $user, School $school): bool
    {
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        // SchoolAdmin may update own school profile only.
        if ($user->hasRole('SchoolAdmin') && (int) $user->school_id === (int) $school->id) {
            if (session()->has('active_school') && session('active_school')) {
                return (int) session('active_school') === (int) $school->id;
            }

            return true;
        }

        return false;
    }

    public function delete(User $user, School $school): bool
    {
        return $user->hasRole('SuperAdmin');
    }

    public function approve(User $user, School $school): bool
    {
        return $user->hasRole('SuperAdmin');
    }

    public function reject(User $user, School $school): bool
    {
        return $user->hasRole('SuperAdmin');
    }

    public function deactivate(User $user, School $school): bool
    {
        return $user->hasRole('SuperAdmin');
    }

    public function reactivate(User $user, School $school): bool
    {
        return $user->hasRole('SuperAdmin');
    }
}
