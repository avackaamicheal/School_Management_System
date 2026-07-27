<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolSubscription;
use App\Models\User;

class SuperAdminController extends Controller
{
    public function index()
    {
        $totalSchools = School::count();
        $pendingApprovals = School::where('approval_status', 'pending')->count();
        $totalUsers = User::count();
        $activeSubscriptions = SchoolSubscription::where('status', 'active')->count();

        $recentSchools = School::latest()->take(5)->get();
        $recentSubscriptions = SchoolSubscription::with('school', 'plan')
            ->latest()->take(5)->get();
        $totalRevenue = SchoolSubscription::sum('amount_paid');

        return view('superadmin.dashboard', compact(
            'totalSchools', 'pendingApprovals', 'totalUsers', 'activeSubscriptions',
            'recentSchools', 'recentSubscriptions', 'totalRevenue'
        ));
    }
}
