<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\AssessmentWeight;
use App\Models\GradeRecord;
use App\Models\School;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use App\Notifications\GradePublishedNotification;
use Illuminate\Http\Request;

class GradeEntryController extends Controller
{
    public function index(Request $request)
    {
        $sections = Section::with('classLevel')->get();
        $subjects = Subject::all();

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
