<?php

namespace App\Http\Controllers;

use App\Models\ClassLevel;
use App\Models\GradeRecord;
use App\Models\Invoice;
use App\Models\School;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request, School $school)
    {
        $query       = $request->get('q');
        $type        = $request->get('type', 'all');
        $classId     = $request->get('class_id');
        $sectionId   = $request->get('section_id');
        $gender      = $request->get('gender');
        $status      = $request->get('status');

        $results = [
            'students'  => collect(),
            'teachers'  => collect(),
            'invoices'  => collect(),
            'grades'    => collect(),
        ];

        $classLevels = ClassLevel::all();
        $sections    = $sectionId
            ? Section::all()
            : ($classId ? Section::where('class_level_id', $classId)->get() : collect());

        if (!$query) {
            return view('search.index', compact(
                'results', 'query', 'type',
                'classLevels', 'sections',
                'classId', 'sectionId', 'gender', 'status'
            ));
        }

        // --- STUDENTS ---
        if ($type === 'all' || $type === 'students') {
            $results['students'] = User::role('Student')
                ->where('school_id', session('active_school'))
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhereHas('studentProfile', function ($q) use ($query) {
                          $q->where('admission_number', 'like', "%{$query}%");
                      });
                })
                ->when($classId, function ($q) use ($classId) {
                    $q->whereHas('studentProfile', function ($q) use ($classId) {
                        $q->where('class_level_id', $classId);
                    });
                })
                ->when($sectionId, function ($q) use ($sectionId) {
                    $q->whereHas('studentProfile', function ($q) use ($sectionId) {
                        $q->where('section_id', $sectionId);
                    });
                })
                ->when($gender, function ($q) use ($gender) {
                    $q->whereHas('studentProfile', function ($q) use ($gender) {
                        $q->where('gender', $gender);
                    });
                })
                ->with(['studentProfile.section.classLevel'])
                ->limit(20)
                ->get();
        }

        // --- TEACHERS ---
        if ($type === 'all' || $type === 'teachers') {
            $results['teachers'] = User::role('Teacher')
                ->where('school_id', session('active_school'))
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%")
                      ->orWhereHas('teacherProfile', function ($q) use ($query) {
                          $q->where('employee_id', 'like', "%{$query}%")
                            ->orWhere('qualification', 'like', "%{$query}%");
                      });
                })
                ->when($gender, function ($q) use ($gender) {
                    $q->whereHas('teacherProfile', function ($q) use ($gender) {
                        $q->where('gender', $gender);
                    });
                })
                ->with(['teacherProfile', 'allocations.subject', 'allocations.section.classLevel'])
                ->limit(20)
                ->get();
        }

        // --- INVOICES ---
        if ($type === 'all' || $type === 'invoices') {
            $results['invoices'] = Invoice::with(['student.studentProfile'])
                ->where(function ($q) use ($query) {
                    $q->where('invoice_number', 'like', "%{$query}%")
                      ->orWhereHas('student', function ($q) use ($query) {
                          $q->where('name', 'like', "%{$query}%");
                      });
                })
                ->when($status, function ($q) use ($status) {
                    $q->where('status', $status);
                })
                ->when($classId, function ($q) use ($classId) {
                    $q->whereHas('student.studentProfile', function ($q) use ($classId) {
                        $q->where('class_level_id', $classId);
                    });
                })
                ->withSum('payments', 'amount')
                ->limit(20)
                ->get();
        }

        // --- GRADES ---
        if ($type === 'all' || $type === 'grades') {
            $results['grades'] = GradeRecord::with(['student', 'subject'])
                ->whereHas('student', function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->where('school_id', session('active_school'));
                })
                ->when($sectionId, function ($q) use ($sectionId) {
                    $q->where('section_id', $sectionId);
                })
                ->when($status, function ($q) use ($status) {
                    if ($status === 'published') $q->where('is_locked', true);
                    if ($status === 'draft') $q->where('is_locked', false);
                })
                ->limit(20)
                ->get();
        }

        $totalResults = collect($results)->sum(fn($r) => $r->count());

        return view('search.index', compact(
            'results', 'query', 'type', 'totalResults',
            'classLevels', 'sections',
            'classId', 'sectionId', 'gender', 'status'
        ));
    }

    // Live search via AJAX for the navbar
    public function live(Request $request, School $school)
    {
        $query = $request->get('q');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $results = [];

        // Students
        $students = User::role('Student')
            ->where('school_id', session('active_school'))
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhereHas('studentProfile', function ($q) use ($query) {
                      $q->where('admission_number', 'like', "%{$query}%");
                  });
            })
            ->with('studentProfile.section.classLevel')
            ->limit(5)
            ->get();

        foreach ($students as $student) {
            $results[] = [
                'type'     => 'Student',
                'icon'     => 'fas fa-user-graduate',
                'color'    => 'info',
                'title'    => $student->name,
                'subtitle' => $student->studentProfile->admission_number ?? 'N/A',
                'meta'     => $student->studentProfile->section->classLevel->name ?? '',
                'url'      => route('student.index'),
            ];
        }

        // Teachers
        $teachers = User::role('Teacher')
            ->where('school_id', session('active_school'))
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->with('teacherProfile')
            ->limit(3)
            ->get();

        foreach ($teachers as $teacher) {
            $results[] = [
                'type'     => 'Teacher',
                'icon'     => 'fas fa-chalkboard-teacher',
                'color'    => 'success',
                'title'    => $teacher->name,
                'subtitle' => $teacher->teacherProfile->employee_id ?? $teacher->email,
                'meta'     => $teacher->teacherProfile->qualification ?? '',
                'url'      => route('teachers.index'),
            ];
        }

        // Invoices
        $invoices = Invoice::where('invoice_number', 'like', "%{$query}%")
            ->orWhereHas('student', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->where('school_id', session('active_school'));
            })
            ->with('student')
            ->limit(3)
            ->get();

        foreach ($invoices as $invoice) {
            $results[] = [
                'type'     => 'Invoice',
                'icon'     => 'fas fa-file-invoice-dollar',
                'color'    => 'warning',
                'title'    => $invoice->invoice_number,
                'subtitle' => $invoice->student->name ?? 'N/A',
                'meta'     => $invoice->status,
                'url'      => route('invoices.index'),
            ];
        }

        return response()->json($results);
    }
}
