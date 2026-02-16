<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Term;
use Illuminate\Http\Request;

class AcademicSettingsController extends Controller
{
    // --- DISPLAY THE UI ---
    public function index()
    {
        // Notice no `where('school_id', ...)`! The trait handles it.
        $sessions = AcademicSession::latest()->get();
        $terms = Term::with('academicSession')->latest()->get();

        return view('academics.settings.index', compact('sessions', 'terms'));
    }

    // --- CREATE A NEW SESSION ---
    public function storeSession(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|unique:academic_sessions,name', // e.g., 2025/2026
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        AcademicSession::create($request->only('name', 'start_date', 'end_date'));

        return back()->with('success', 'Academic Session created successfully.');
    }

    // --- CREATE A NEW TERM ---
    public function storeTerm(Request $request)
    {
        $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'name'                => 'required|string', // e.g., First Term
        ]);

        Term::create($request->only('academic_session_id', 'name'));

        return back()->with('success', 'Term created successfully.');
    }

    // --- ACTIVATE METHODS (From earlier) ---
    public function activateSession(AcademicSession $academicSession)
    {
        $academicSession->makeActive();
        return back()->with('success', "{$academicSession->name} is now the active academic session.");
    }

    public function activateTerm(Term $term)
    {
        $term->makeActive();
        return back()->with('success', "{$term->name} is now the active term.");
    }
}

