<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/login';
    /**
     * Get the post login redirect path.
     *
     * @return string
     */
    public function redirectTo()
    {
        $user = Auth::user();

        // SuperAdmin first — no school needed
        if ($user->hasRole('SuperAdmin')) {
            return route('superadmin.dashboard');
        }

        // All other roles need a school
        $school = $user->school;
        $slug = trim($school->slug ?? '');

        if ($user->hasRole('SchoolAdmin')) {
            // Manually deactivated
            if ($school?->approval_status === 'rejected') {
                return route('schooladmin.rejected');
            }

            // No active subscription
            if (!$school->hasActiveSubscription()) {
                return route('subscription.index');
            }

            if (!$slug) {
                Auth::logout();
                return route('login');
            }

            return route('schooladmin.dashboard', ['school' => $slug]);
        }

        // Remaining roles need a valid slug
        if (!$slug) {
            Auth::logout();
            return route('login');
        }

        if ($user->hasRole('Teacher')) {
            return route('teacher.dashboard', ['school' => $slug]);
        }

        if ($user->hasRole('Student')) {
            return route('student.dashboard', ['school' => $slug]);
        }

        if ($user->hasRole('Parent')) {
            return route('parent.dashboard', ['school' => $slug]);
        }

        if ($user->hasRole('Bursar')) {
            return route('bursar.dashboard', ['school' => $slug]);
        }

        Auth::logout();
        return route('login');
    }



    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
