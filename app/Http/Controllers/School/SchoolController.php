<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSchoolRequest;
use App\Http\Requests\UpdateSchoolRequest;
use App\Models\School;
use App\Models\User;
use App\Notifications\SchoolApprovedNotification;
use App\Notifications\SchoolRejectedNotification;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schools = School::all();

        return view('school.index', compact('schools'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('school.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSchoolRequest $request)
    {
        $validatedData = $request->validated();


        School::create($validatedData);

        return redirect()->route('school.index')->with('success', 'School created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(School $school)
    {
        return view('school.show', compact('school'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(School $school)
    {
        return view('school.edit', compact('school'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSchoolRequest $request, School $school)
    {
        $validatedData = $request->validated();

        // Option A: Update only specific fields from the validated array
        $school->update($validatedData);

        return redirect()->route('school.index')->with('success', 'School updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(School $school)
    {
        $school->delete();

        return redirect()->route('school.index')->with('success', 'School deleted successfully');
    }

    public function approve(School $school)
    {
        $school->update([
            'is_active' => true,
            'approval_status' => 'approved',
            'approved_at' => now(),
        ]);

        // Notify the school admin
        $admin = User::where('school_id', $school->id)
            ->role('SchoolAdmin')
            ->first();

        if ($admin) {
            $admin->notify(new SchoolApprovedNotification($school));
        }

        return back()->with('success', "{$school->name} has been approved!");
    }

    public function reject(Request $request, School $school)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $school->update([
            'is_active' => false,
            'approval_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Notify the school admin
        $admin = User::where('school_id', $school->id)
            ->role('SchoolAdmin')
            ->first();

        if ($admin) {
            $admin->notify(new SchoolRejectedNotification($school, $request->rejection_reason));
        }

        return back()->with('success', "{$school->name} has been rejected.");
    }

    public function deactivate(School $school)
    {
        $school->update([
            'is_active' => false,
            'approval_status' => 'rejected',
            'rejection_reason' => 'Manually deactivated by SuperAdmin.',
        ]);

        // Notify school admin
        $admin = User::where('school_id', $school->id)
            ->role('SchoolAdmin')
            ->first();

        if ($admin) {
            $admin->notify(new SchoolRejectedNotification(
                $school,
                'Your school has been deactivated by the administrator. Please contact support.'
            ));
        }

        return back()->with('success', "{$school->name} has been deactivated.");
    }

    public function reactivate(School $school)
    {
        // Only reactivate if they have a valid subscription
        if (!$school->hasActiveSubscription()) {
            return back()->with('error', "{$school->name} has no active subscription. They must renew first.");
        }

        $school->update([
            'is_active' => true,
            'approval_status' => 'approved',
            'rejection_reason' => null,
        ]);

        $admin = User::where('school_id', $school->id)
            ->role('SchoolAdmin')
            ->first();

        if ($admin) {
            $admin->notify(new SchoolApprovedNotification($school));
        }

        return back()->with('success', "{$school->name} has been reactivated.");
    }
}
