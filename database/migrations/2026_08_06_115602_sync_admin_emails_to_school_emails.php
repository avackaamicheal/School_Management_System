<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * For each school whose SchoolAdmin account still logs in with a
     * personal email, switch the primary admin to the school's email so
     * registration and login use the school email going forward.
     */
    public function up(): void
    {
        $admins = DB::table('users')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'users.id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('schools', 'schools.id', '=', 'users.school_id')
            ->where('roles.name', 'SchoolAdmin')
            ->whereNotNull('schools.email')
            ->where('schools.email', '!=', '')
            ->select('users.id as user_id', 'users.email as user_email', 'schools.email as school_email')
            ->orderBy('users.id')
            ->get();

        foreach ($admins as $admin) {
            if (strtolower($admin->user_email) === strtolower($admin->school_email)) {
                continue;
            }

            $emailTaken = DB::table('users')
                ->where('email', $admin->school_email)
                ->where('id', '!=', $admin->user_id)
                ->exists();

            if ($emailTaken) {
                continue;
            }

            DB::table('users')
                ->where('id', $admin->user_id)
                ->update([
                    'email' => $admin->school_email,
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Previous admin emails are not recoverable; nothing to reverse.
    }
};
