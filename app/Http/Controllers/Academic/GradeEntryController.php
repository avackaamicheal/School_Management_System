<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\AssessmentWeight;
use App\Models\ClassroomAssignment;
use App\Models\GradeRecord;
use App\Models\School;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use App\Notifications\GradePublishedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeEntryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $allocations = $user->allocations()->get();

        $sectionIds = $allocations->pluck('section_id')->unique()->toArray();
        $subjectIds = $allocations->pluck('subject_id')->unique()->toArray();

        $sections = Section::with('classLevel')->whereIn('id', $sectionIds)->get();
        $subjects = Subject::all()->whereIn('id', $subjectIds);

        $subjectsBySection = $allocations->groupBy('section_id')
            ->map(fn ($items) => $items->pluck('subject_id')->unique()->values()->toArray());

        if ($request->has('section_id') && $request->has('subject_id')) {
            $isAssignedPair = $allocations->contains(function ($allocation) use ($request) {
                return (int) $allocation->section_id === (int) $request->section_id
                    && (int) $allocation->subject_id === (int) $request->subject_id;
            });

            if (!$isAssignedPair) {
                abort(403, 'Unauthorized: You can only grade subjects and students assigned to you.');
            }
        }

        $selectedSection = null;
        $selectedSubject = null;
        $students = collect();
        $weights = collect();
        $existingGrades = [];
        $isLocked = false;

        $activeTerm = Term::getActive();

        if ($request->has('section_id') && $request->has('subject_id')) {
            $selectedSection = Section::find($request->section_id);
            $selectedSubject = Subject::find($request->subject_id);

            // 1. Get the grading formula for this specific subject
            $weights = AssessmentWeight::where('subject_id', $selectedSubject->id)->get();

            if ($weights->sum('weight') !== 100) {
                return back()->with('error', 'The assessment weights for this subject do not equal 100%. Please configure them in Assessment Setup first.');
            }

            // 2. Get the students in this class section
            $students = User::role('Student')->whereHas('studentProfile', function ($query) use ($selectedSection) {
                $query->where('section_id', $selectedSection->id);
            })->get();

            // 3. Get any existing grades and check if the sheet is locked
            if ($activeTerm) {
                $records = GradeRecord::where('term_id', $activeTerm->id)
                    ->where('section_id', $selectedSection->id)
                    ->where('subject_id', $selectedSubject->id)
                    ->get();

                $existingGrades = $records->keyBy('student_id');

                // If even one record is locked, lock the whole view for this subject
                $isLocked = $records->where('is_locked', true)->count() > 0;
            }
        }

        return view('academics.grades.index', compact(
            'sections',
            'subjects',
            'subjectsBySection',
            'selectedSection',
            'selectedSubject',
            'students',
            'weights',
            'existingGrades',
            'isLocked'
        ));
    }

    public function store(Request $request, School $school)
    {
        $activeTerm = Term::getActive();

        if (!$activeTerm) {
            return back()->with('error', 'No active term found.');
        }

        $user = Auth::user();

        $isAssigned = ClassroomAssignment::where('teacher_id', $user->id)
            ->where('section_id', $request->section_id)
            ->where('subject_id', $request->subject_id)
            ->exists();

        if (!$isAssigned) {
            abort(403, 'Unauthorized: You can only grade subjects and students assigned to you.');
        }

        $studentIds = array_keys($request->grades ?? []);

        if (!empty($studentIds)) {
            $assignedStudentsCount = User::role('Student')
                ->whereIn('id', $studentIds)
                ->whereHas('studentProfile', function ($query) use ($request) {
                    $query->where('section_id', $request->section_id);
                })
                ->count();

            if ($assignedStudentsCount !== count($studentIds)) {
                abort(403, 'Unauthorized: You can only grade students in your assigned classes.');
            }
        }

        $lockGrades = $request->has('publish_grades');

        foreach ($request->grades as $studentId => $scores) {
            $totalScore = array_sum($scores);

            $grade = GradeRecord::updateOrCreate(
                [
                    'term_id' => $activeTerm->id,
                    'section_id' => $request->section_id,
                    'subject_id' => $request->subject_id,
                    'student_id' => $studentId,
                ],
                [
                    'scores' => $scores,
                    'total_score' => $totalScore,
                    'is_locked' => $lockGrades,
                ]
            );

            // Only notify when publishing/locking
            if ($lockGrades) {
                $student = User::with('parents')->find($studentId);

                if ($student) {
                    // Notify student
                    $student->notify(new GradePublishedNotification($grade));

                    // Notify parents
                    foreach ($student->parents as $parent) {
                        $parent->notify(new GradePublishedNotification($grade));
                    }
                }
            }
        }

        $message = $lockGrades
            ? 'Grades published and locked successfully!'
            : 'Grade draft saved successfully!';

        return back()->with('success', $message);
    }
}
