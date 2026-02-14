<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Term;

class AcademicSettingsController extends Controller
{
    public function activateSession(AcademicSession $academicSession)
    {
        // Ensure the admin actually belongs to the school of the session they are activating
        if ($academicSession->school_id !== session('active_school')) {
            abort(403, 'Unauthorized action.');
        }

        $academicSession->makeActive();

        return back()->with('success', "{$academicSession->name} is now the active academic session.");
    }

    public function activateTerm(Term $term)
    {
        if ($term->school_id !== session('active_school')) {
            abort(403, 'Unauthorized action.');
        }

        $term->makeActive();

        return back()->with('success', "{$term->name} is now the active term.");
    }
}

