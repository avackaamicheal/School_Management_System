<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\School;
use App\Models\Term;
use App\Models\Timetable;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalController extends Controller
{
    /**
     * Fetch all children linked to the authenticated parent,
     * with their profiles, published grades, and invoices for the active term.
     */
    private function loadChildren(?Term $activeTerm)
    {
        $parent = Auth::user();

        return User::role('Student')
            ->whereHas('studentProfile', function ($query) use ($parent) {
                $query->where('parent_id', $parent->id);
            })
            ->with([
                'studentProfile.section.classLevel',
                'grades' => function ($q) use ($activeTerm) {
                    $q->where('term_id', $activeTerm?->id)
                      ->where('is_locked', true)
                      ->with('subject');
                },
                'invoices' => function ($q) use ($activeTerm) {
                    $q->where('term_id', $activeTerm?->id)
                      ->withSum('payments', 'amount')
                      ->with('items');
                },
            ])
            ->get();
    }

    private function monthlyAttendanceBreakdown(int $studentId)
    {
        $breakdown = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $records = Attendance::where('student_id', $studentId)
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->get();

            $total   = $records->count();
            $present = $records->where('status', 'PRESENT')->count();

            $breakdown->push([
                'month'   => $month->format('M'),
                'total'   => $total,
                'present' => $present,
                'absent'  => $records->where('status', 'ABSENT')->count(),
                'late'    => $records->where('status', 'LATE')->count(),
                'rate'    => $total > 0 ? round(($present / $total) * 100, 1) : 0,
            ]);
        }

        return $breakdown;
    }

    private function termAttendanceSummary(int $studentId, ?Term $activeTerm)
    {
        $records = Attendance::where('student_id', $studentId)
            ->where('term_id', $activeTerm?->id)
            ->get();

        $total   = $records->count();
        $present = $records->where('status', 'PRESENT')->count();
        $rate    = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        return [
            'total'   => $total,
            'present' => $present,
            'absent'  => $records->where('status', 'ABSENT')->count(),
            'late'    => $records->where('status', 'LATE')->count(),
            'rate'    => $rate,
        ];
    }

    public function children(Request $request, School $school)
    {
        $activeTerm = Term::getActive();
        $children = $this->loadChildren($activeTerm);

        $childrenData = $children->map(function ($child) {
            $totalBilled = $child->invoices->sum('total_amount');
            $totalPaid   = $child->invoices->sum('payments_sum_amount');
            $average     = $child->grades->count() > 0
                ? round($child->grades->avg('total_score'), 2)
                : null;

            return [
                'student'            => $child,
                'section'            => $child->studentProfile->section,
                'classLevel'         => $child->studentProfile->section->classLevel,
                'outstandingBalance' => max(0, $totalBilled - $totalPaid),
                'average'            => $average,
            ];
        });

        return view('parent.children', compact('children', 'childrenData', 'activeTerm'));
    }

    public function results(Request $request, School $school)
    {
        $activeTerm = Term::getActive();
        $children = $this->loadChildren($activeTerm);

        $childrenData = $children->map(function ($child) {
            $average = $child->grades->count() > 0
                ? round($child->grades->avg('total_score'), 2)
                : null;

            return [
                'student'    => $child,
                'section'    => $child->studentProfile->section,
                'classLevel' => $child->studentProfile->section->classLevel,
                'grades'     => $child->grades->sortBy(fn($g) => $g->subject->name ?? ''),
                'average'    => $average,
            ];
        });

        return view('parent.results', compact('children', 'childrenData', 'activeTerm'));
    }

    public function attendance(Request $request, School $school)
    {
        $activeTerm = Term::getActive();
        $children = $this->loadChildren($activeTerm);

        $childrenData = $children->map(function ($child) use ($activeTerm) {
            return [
                'student'      => $child,
                'section'      => $child->studentProfile->section,
                'classLevel'   => $child->studentProfile->section->classLevel,
                'summary'      => $this->termAttendanceSummary($child->id, $activeTerm),
                'monthly'      => $this->monthlyAttendanceBreakdown($child->id),
                'today'        => Attendance::where('student_id', $child->id)
                    ->where('date', now()->format('Y-m-d'))
                    ->first(),
            ];
        });

        return view('parent.attendance', compact('children', 'childrenData', 'activeTerm'));
    }

    public function timetable(Request $request, School $school)
    {
        $activeTerm = Term::getActive();
        $children = $this->loadChildren($activeTerm);

        $childrenData = $children->map(function ($child) use ($activeTerm) {
            $slots = $activeTerm
                ? Timetable::with(['subject', 'teacher'])
                    ->where('term_id', $activeTerm->id)
                    ->where('section_id', $child->studentProfile->section_id)
                    ->orderBy('start_time')
                    ->get()
                    ->groupBy('day_of_week')
                : collect();

            return [
                'student'    => $child,
                'section'    => $child->studentProfile->section,
                'classLevel' => $child->studentProfile->section->classLevel,
                'slots'      => $slots,
            ];
        });

        return view('parent.timetable', compact('children', 'childrenData', 'activeTerm'));
    }

    public function fees(Request $request, School $school)
    {
        $activeTerm = Term::getActive();
        $children = $this->loadChildren($activeTerm);

        $childrenData = $children->map(function ($child) {
            $invoices = $child->invoices->sortBy('due_date');
            $invoices->load('payments');
            $totalBilled = $invoices->sum('total_amount');
            $totalPaid   = $invoices->sum('payments_sum_amount');

            return [
                'student'            => $child,
                'section'            => $child->studentProfile->section,
                'classLevel'         => $child->studentProfile->section->classLevel,
                'invoices'           => $invoices,
                'totalBilled'        => $totalBilled,
                'totalPaid'          => $totalPaid,
                'outstandingBalance' => max(0, $totalBilled - $totalPaid),
            ];
        });

        return view('parent.fees', compact('children', 'childrenData', 'activeTerm'));
    }

    public function teachers(Request $request, School $school)
    {
        $teachers = User::role('Teacher')
            ->where('school_id', session('active_school'))
            ->with([
                'teacherProfile',
                'allocations.subject',
                'allocations.section.classLevel',
            ])
            ->orderBy('name')
            ->get();

        return view('parent.teachers', compact('teachers'));
    }
}
