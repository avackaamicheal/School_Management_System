<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSchoolApproval
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        if ($user->hasRole('SchoolAdmin')) {
            $school = $user->school;

            $allowedRoutes = [
                'subscription.index',
                'subscription.initiate',
                'subscription.callback',
                'schooladmin.rejected',
                'logout',
            ];

            if ($request->routeIs(...$allowedRoutes)) {
                return $next($request);
            }

            // School manually deactivated by SuperAdmin
            if ($school?->approval_status === 'rejected') {
                return redirect()->route('schooladmin.rejected');
            }

            // No active subscription — redirect to subscribe
            if (!$school->hasActiveSubscription()) {
                return redirect()->route('subscription.index');
            }
        }

        return $next($request);
    }
}
