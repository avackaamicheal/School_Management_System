<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SearchFilter extends Component
{
    public function __construct(
        public string $route,
        public string $placeholder = 'Search...',
        // public bool $showClass = false,
        // public bool $showSection = false,
        // public bool $showGender = false,
        // public bool $showStatus = false,
        // public bool $showTerm = false,
        // public $classLevels = null,
        // public $sections = null,
        // public $terms = null,
        // public array $statusOptions = [],
    ) {}

    public function render()
    {
        return view('components.search-filter');
    }
}
