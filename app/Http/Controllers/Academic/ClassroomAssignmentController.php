<?php

namespace App\Http\Controllers\Academic;

use App\Models\User;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Models\ClassroomAssignment;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class ClassroomAssignmentController extends Controller
{

    public function index()
    {
        // Get all assignments with names loaded
        $assignments = ClassroomAssignment::with(['teacher', 'section.classLevel', 'subject'])
            ->latest()
            ->paginate(15);

        // Get dropdown data
        // Note: In a real app, scope Users by role ('Teacher') and active school
        $teachers = User::where('role', 'teacher')->where('school_id', session('active_school'))->get();
        $sections = Section::with('classLevel')->get();
        $subjects = Subject::all();

        return view('academic.assignments.index', compact('assignments', 'teachers', 'sections', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => ['required', Rule::exists('users', 'id')->where('school_id', session('active_school'))],
            'section_id' => ['required', Rule::exists('sections', 'id')->where('school_id', session('active_school'))],
            'subject_id' => ['required', Rule::exists('subjects', 'id')->where('school_id', session('active_school'))],
        ]);

        try {
            ClassroomAssignment::create($request->only(['teacher_id', 'section_id', 'subject_id']));

            return response()->json([
                'status' => 'success',
                'message' => 'Teacher assigned successfully!'
            ]);
        } catch (\Exception $e) {
            // Catch the unique constraint violation (Duplicate assignment)
            return response()->json([
                'status' => 'error',
                'message' => 'This teacher is already assigned to this subject in this section.'
            ], 422);
        }
    }

    public function destroy(ClassroomAssignment $assignment)
    {
        if ((int) $assignment->school_id !== (int) session('active_school')) {
            abort(403, 'Assignment does not belong to this school.');
        }

        $assignment->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Assignment removed.']);
        }
        return back()->with('success', 'Assignment removed.');
    }
}
