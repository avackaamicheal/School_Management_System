<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportCardController extends Controller
{
    // 1. Show the Class Selection and Student List
    public function index(Request $request)
    {
        $sections = Section::with('classLevel')->get();
        $selectedSection = null;
        $students = collect();

        if ($request->has('section_id')) {
            $selectedSection = Section::find($request->section_id);
            $students = User::role('Student')->whereHas('studentProfile', function($query) use ($selectedSection) {
                $query->where('section_id', $selectedSection->id);
            })->with('studentProfile')->get();
        }

        return view('academic.reports.index', compact('sections', 'selectedSection', 'students'));
    }

    // 2. Download a Single Student's Report
    public function downloadSingle(Request $request, $studentId)
    {
        $student = User::with('studentProfile')->findOrFail($studentId);
        $activeTerm = Term::where('is_active', true)->firstOrFail();

        $grades = \App\Models\GradeRecord::with('subject')
            ->where('student_id', $student->id)
            ->where('term_id', $activeTerm->id)
            ->get();

        $reportData = $this->compileReportData($student, $grades, $activeTerm);

        // Load the Blade view and pass the data
        $pdf = Pdf::loadView('academic.reports.pdf', ['reports' => [$reportData]]);

        return $pdf->download(str_replace(' ', '_', $student->name) . '_Report_Card.pdf');
    }

    // 3. Batch Download an Entire Class
    public function downloadBatch(Request $request, $sectionId)
    {
        $section = Section::with('classLevel')->findOrFail($sectionId);
        $activeTerm = Term::where('is_active', true)->firstOrFail();

        $students = User::role('Student')->whereHas('studentProfile', function($query) use ($section) {
            $query->where('section_id', $section->id);
        })->with('studentProfile')->get();

        $reports = [];
        foreach ($students as $student) {
            $grades = \App\Models\GradeRecord::with('subject')
                ->where('student_id', $student->id)
                ->where('term_id', $activeTerm->id)
                ->get();

            // Only generate a card if they actually have grades
            if ($grades->count() > 0) {
                $reports[] = $this->compileReportData($student, $grades, $activeTerm);
            }
        }

        $pdf = Pdf::loadView('academic.reports.pdf', ['reports' => $reports]);

        return $pdf->download(str_replace(' ', '_', $section->name) . '_Batch_Reports.pdf');
    }

    // --- Helper Method: The Grading Engine ---
    private function compileReportData($student, $grades, $term)
    {
        $totalScore = $grades->sum('total_score');
        $subjectCount = $grades->count();
        $average = $subjectCount > 0 ? round($totalScore / $subjectCount, 2) : 0;

        // Attach Letter Grade and Remark to each subject
        foreach ($grades as $grade) {
            $grade->letter = $this->getLetterGrade($grade->total_score);
            $grade->remark = $this->getRemark($grade->total_score);
        }

        return [
            'student' => $student,
            'term'    => $term,
            'grades'  => $grades,
            'average' => $average,
            'overall_grade' => $this->getLetterGrade($average)
        ];
    }

    private function getLetterGrade($score) {
        if ($score >= 70) return 'A';
        if ($score >= 60) return 'B';
        if ($score >= 50) return 'C';
        if ($score >= 40) return 'D';
        return 'F';
    }

    private function getRemark($score) {
        if ($score >= 70) return 'Excellent';
        if ($score >= 60) return 'Very Good';
        if ($score >= 50) return 'Good';
        if ($score >= 40) return 'Pass';
        return 'Needs Improvement';
    }
}
