<?php

namespace App\Http\Controllers;

use App\Models\ClassLevel;
use App\Models\School;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuickSetupController extends Controller
{
    protected array $presets = [
        'secondary' => [
            'label' => 'Nigerian Secondary School',
            'classes' => ['JSS1', 'JSS2', 'JSS3', 'SS1', 'SS2', 'SS3'],
        ],
        'primary' => [
            'label' => 'Nigerian Primary School',
            'classes' => ['Primary 1', 'Primary 2', 'Primary 3', 'Primary 4', 'Primary 5', 'Primary 6'],
        ],
        'nursery' => [
            'label' => 'Nursery / Kindergarten',
            'classes' => ['Creche', 'Nursery 1', 'Nursery 2', 'KG1', 'KG2'],
        ],
    ];

    protected array $coreSubjects = [
        'Mathematics' => 'MTH',
        'English Language' => 'ENG',
        'Basic Science' => 'BSC',
        'Social Studies' => 'SOC',
        'Civic Education' => 'CIV',
        'Computer Studies' => 'COMP',
        'Physical and Health Education' => 'PHE',
        'Creative Arts' => 'ART',
    ];

    public function show(School $school)
    {
        return view('setup.quick-setup', [
            'presets' => $this->presets,
        ]);
    }

    public function apply(Request $request, School $school)
    {
        $request->validate([
            'preset' => 'required|in:secondary,primary,nursery,scratch',
        ]);

        if ($request->preset === 'scratch') {
            return redirect()->route('classLevel.index')
                ->with('success', 'No problem! Start by creating your first class level.');
        }

        $schoolId = session('active_school');
        $preset = $this->presets[$request->preset];

        DB::beginTransaction();

        try {
            foreach ($preset['classes'] as $className) {
                $classLevel = ClassLevel::create([
                    'school_id' => $schoolId,
                    'name' => $className,
                ]);

                // One default section per class
                Section::create([
                    'school_id' => $schoolId,
                    'class_level_id' => $classLevel->id,
                    'name' => 'A',
                    'capacity' => 40,
                    'is_active' => true,
                ]);
            }

            // Core subjects (only create if they don't already exist)
            foreach ($this->coreSubjects as $subjectName => $code) {
                Subject::firstOrCreate(
                    [
                        'school_id' => $schoolId,
                        'name' => $subjectName,
                    ],
                    [
                        'code' => $code
                    ]

                );
            }

            DB::commit();

            return redirect()->route('schooladmin.dashboard', ['school' => $school->slug])
                ->with('success', "Quick setup complete! Created {$preset['label']} structure with " . count($preset['classes']) . " classes and " . count($this->coreSubjects) . " subjects.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Setup failed: ' . $e->getMessage());
        }
    }
}
