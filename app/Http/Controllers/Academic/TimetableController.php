<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTimetableRequest;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Term;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Http\Request;


class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $sections = Section::with('classLevel')->get();
        $subjects = Subject::get();
        $teachers = User::role('Teacher')->get();
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        $timetableGrid = [];
        $activeFilter = null; // Will be 'section' or 'teacher'
        $selectedEntity = null; // Will hold the specific Section or Teacher model

        $activeTerm = Term::where('is_active', true)->first();

        // SCENARIO A: Searching by Class Section
        if ($request->has('section_id') && $request->section_id != '') {
            $activeFilter = 'section';
            $selectedEntity = Section::with('classLevel')->find($request->section_id);

            if ($activeTerm && $selectedEntity) {
                $rawTimetable = Timetable::with(['subject', 'teacher'])
                    ->where('term_id', $activeTerm->id)
                    ->where('section_id', $selectedEntity->id)
                    ->orderBy('start_time')
                    ->get();
                $timetableGrid = $rawTimetable->groupBy('day_of_week');
            }
        }
        // SCENARIO B: Searching by Teacher
        elseif ($request->has('teacher_id') && $request->teacher_id != '') {
            $activeFilter = 'teacher';
            $selectedEntity = User::find($request->teacher_id);

            if ($activeTerm && $selectedEntity) {
                // Notice we load 'section.classLevel' here instead of 'teacher'
                $rawTimetable = Timetable::with(['subject', 'section.classLevel'])
                    ->where('term_id', $activeTerm->id)
                    ->where('teacher_id', $selectedEntity->id)
                    ->orderBy('start_time')
                    ->get();
                $timetableGrid = $rawTimetable->groupBy('day_of_week');
            }
        }

        return view('academics.timetable.index', compact(
            'sections', 'subjects', 'teachers', 'daysOfWeek',
            'timetableGrid', 'activeFilter', 'selectedEntity'
        ));
    }


    public function store(StoreTimetableRequest $request)
    {
        $validatedData = $request->validated();

        // 1. Get the currently active term
        $activeTerm = Term::where('is_active', true)->first();
        if (!$activeTerm) {
            return back()->with('error', 'Please set an active Term in Academic Settings first.');
        }

        // 2. CHECK FOR TEACHER OVERLAP
        // A teacher cannot be in two places at once
        $teacherConflict = Timetable::where('term_id', $activeTerm->id)
            ->where('teacher_id', $request->teacher_id)
            ->where('day_of_week', $request->day_of_week)
            ->where('start_time', '<', $request->end_time)
            ->where('end_time', '>', $request->start_time)
            ->exists();

        if ($teacherConflict) {
            return back()->with('error', 'Schedule Conflict: This teacher already has a class scheduled during this time on ' . $request->day_of_week);
        }

        // 3. CHECK FOR SECTION OVERLAP
        // A section (class) cannot have two subjects taught at the same time
        $sectionConflict = Timetable::where('term_id', $activeTerm->id)
            ->where('section_id', $request->section_id)
            ->where('day_of_week', $request->day_of_week)
            ->where('start_time', '<', $request->end_time)
            ->where('end_time', '>', $request->start_time)
            ->exists();

        if ($sectionConflict) {
            return back()->with('error', 'Schedule Conflict: This section already has a class scheduled during this time on ' . $request->day_of_week);
        }

        // 4. Save if no conflicts
        Timetable::create([
            'term_id' => $activeTerm->id,
            'section_id' => $request->section_id,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return back()->with('success', 'Timetable slot created successfully!');
    }

    // Add a destroy method so admins can delete mistaken slots
    public function destroy(Timetable $timetable)
    {
        $timetable->delete();
        return back()->with('success', 'Timetable slot removed.');
    }
}
