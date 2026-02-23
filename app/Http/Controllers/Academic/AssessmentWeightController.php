<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\AssessmentWeight;
use App\Models\Subject;
use Illuminate\Http\Request;

class AssessmentWeightController extends Controller
{
    public function index(Request $request)
    {
        $subjects = Subject::all();
        $selectedSubject = null;
        $weights = collect();

        // If the admin selected a subject, load its existing weights
        if ($request->has('subject_id') && $request->subject_id != '') {
            $selectedSubject = Subject::findOrFail($request->subject_id);
            $weights = AssessmentWeight::where('subject_id', $selectedSubject->id)->get();
        }

        return view('academics.assessments.index', compact('subjects', 'selectedSubject', 'weights'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'categories' => 'required|array',
            'categories.*.name' => 'required|string|max:255',
            'categories.*.weight' => 'required|numeric|min:1|max:100',
        ]);

        // 1. Calculate the total weight
        $totalWeight = collect($request->categories)->sum('weight');

        // 2. The 100% Validation Rule
        if ($totalWeight != 100) {
            return back()->with('error', "Validation Failed: The total weight must be exactly 100. Your current total is {$totalWeight}.");
        }

        // 3. If valid, wipe the old weights for this subject and save the new ones
        AssessmentWeight::where('subject_id', $request->subject_id)->delete();

        foreach ($request->categories as $category) {
            AssessmentWeight::create([
                'subject_id' => $request->subject_id,
                'name' => $category['name'],
                'weight' => $category['weight']
            ]);
        }

        return back()->with('success', 'Assessment weights saved successfully!');
    }
}
