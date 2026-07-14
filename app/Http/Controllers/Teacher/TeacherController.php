<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\ClassLevel;
use App\Models\ClassroomAssignment;
use App\Models\School;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Notifications\NewAssignmentNotification;
use App\Notifications\TeacherAddedNotification;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    public function index(School $school, Request $request)
    {
        $teachers = User::role('Teacher')
            ->where('school_id', session('active_school'))
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhereHas('teacherProfile', function ($q) use ($request) {
                        $q->where('employee_id', 'like', "%{$request->search}%")
                            ->orWhere('qualification', 'like', "%{$request->search}%");
                    });
            })
            ->when($request->gender, function ($q) use ($request) {
                $q->whereHas('teacherProfile', function ($q) use ($request) {
                    $q->where('gender', $request->gender);
                });
            })
            ->with(['teacherProfile', 'allocations.subject', 'allocations.section.classLevel'])
            ->paginate(15)
            ->withQueryString();

        return view('teacher.index', compact('teachers'));
    }

    public function create(School $school)
    {
        return view('teacher.create');
    }

    public function store(StoreTeacherRequest $request, School $school)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'school_id' => session('active_school'),
        ]);

        $user->assignRole('Teacher');

        $picturePath = null;
        if ($request->hasFile('profile_picture')) {
            $picturePath = $request->file('profile_picture')->store('teacher-profiles', 'public');
        }

        TeacherProfile::create([
            'user_id' => $user->id,
            'school_id' => session('active_school'),
            'profile_picture' => $picturePath,
            'employee_id' => TeacherProfile::generateEmployeeId(),
            'qualification' => $request->qualification,
            'hire_date' => $request->hire_date,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'marital_status' => $request->marital_status,
            'address' => $request->address,
        ]);

        // Notify SchoolAdmin — wrapped separately
        try {
            $schoolAdmin = User::where('school_id', session('active_school'))
                ->role('SchoolAdmin')
                ->first();

            if ($schoolAdmin) {
                $schoolAdmin->notify(new TeacherAddedNotification($user));
            }
        } catch (\Exception $e) {
            Log::warning("TeacherAdded notification failed: " . $e->getMessage());
        }

        return redirect()->route('teachers.index')
            ->with('success', 'Teacher added successfully!');
    }

    public function edit(School $school, User $teacher)
    {
        $teacher->load('teacherProfile');
        return view('teacher.edit', compact('teacher'));
    }

    public function update(UpdateTeacherRequest $request, School $school, User $teacher)
    {
        $teacher->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $teacher->update(['password' => Hash::make($request->password)]);
        }

        $picturePath = $teacher->teacherProfile->profile_picture;
        if ($request->hasFile('profile_picture')) {
            if ($picturePath) {
                Storage::disk('public')->delete($picturePath);
            }
            $picturePath = $request->file('profile_picture')->store('teacher-profiles', 'public');
        }

        $teacher->teacherProfile->update([
            'qualification' => $request->qualification,
            'hire_date' => $request->hire_date,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'marital_status' => $request->marital_status,
            'address' => $request->address,
            'profile_picture' => $picturePath,
        ]);

        return redirect()->route('teachers.index')
            ->with('success', 'Teacher updated successfully!');
    }

    public function destroy(School $school, User $teacher)
    {
        if ($teacher->teacherProfile?->profile_picture) {
            Storage::disk('public')->delete($teacher->teacherProfile->profile_picture);
        }

        $teacher->delete();

        return back()->with('success', 'Teacher removed successfully!');
    }

    public function assignments(School $school)
    {
        $teachers = User::role('Teacher')
            ->where('school_id', session('active_school'))
            ->with([
                'teacherProfile',
                'allocations.subject',
                'allocations.section.classLevel'
            ])
            ->get();

        $classLevels = ClassLevel::with('sections')->get();
        $subjects = Subject::all();

        return view('teacher.assignments', compact('teachers', 'classLevels', 'subjects'));
    }

    public function assign(School $school, User $teacher, Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        try {
            $allocation = ClassroomAssignment::create([
                'teacher_id' => $teacher->id,
                'subject_id' => $request->subject_id,
                'section_id' => $request->section_id,
            ]);

            // Notify the teacher of their new assignment
            try {
                $allocation->load(['subject', 'section.classLevel']);
                $teacher->notify(new NewAssignmentNotification($allocation));

            } catch (\Exception $e) {
                Log::warning("NewAssignment notification failed for teacher {$teacher->id}: " . $e->getMessage());
            }

            return back()->with('success', $teacher->name . ' assigned successfully!');

        } catch (QueryException $e) {
            return back()->with('error', 'This teacher is already assigned to this subject in this section.');
        }
    }

    public function destroyAllocation(School $school, User $teacher, ClassroomAssignment $allocation)
    {
        $allocation->delete();
        return back()->with('success', 'Assignment removed successfully!');
    }
}
