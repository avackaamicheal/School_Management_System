<?php

namespace App\View\Components;

use App\Models\School;
use Illuminate\View\Component;

class SetupChecklist extends Component
{
    public array $status;
    public bool $isComplete;
    public School $school;

    public function __construct(School $school)
    {
        $this->school = $school;
        $this->status = $school->setupStatus();
        $this->isComplete = $school->isSetupComplete();
    }

    public function render()
    {
        return view('components.setup-checklist');
    }
}
