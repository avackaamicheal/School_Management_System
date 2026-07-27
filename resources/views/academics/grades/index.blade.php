@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0">Grade Entry Form</h1>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                {{-- <x-search-filter :route="route('admin.grades.index')" placeholder="Search by student name..." :show-section="true"
                    :show-status="true" :status-options="['published' => 'Published', 'draft' => 'Draft']" :sections="$sections" /> --}}

                <div class="card card-default">
                    <div class="card-body">
                        <form action="{{ resolveRoute('grades.index') }}" method="GET" class="row align-items-end">
                            <div class="col-12 col-sm-auto form-group mb-2 mb-sm-0 mr-sm-3">
                                <label class="mr-2 d-block d-sm-inline">Class Section:</label>
                                <select name="section_id" class="form-control" required>
                                    <option value="">-- Choose Class --</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}"
                                            {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                            {{ $section->classLevel->name }} - {{ $section->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-sm-auto form-group mb-2 mb-sm-0 mr-sm-3">
                                <label class="mr-2 d-block d-sm-inline">Subject:</label>
                                <select name="subject_id" class="form-control" required>
                                    <option value="">-- Choose Subject --</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}"
                                            {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-sm-auto form-group mb-2 mb-sm-0">
                                <button type="submit" class="btn btn-primary">Load Gradebook</button>
                            </div>
                        </form>
                    </div>
                </div>

                @if ($selectedSection && $selectedSubject)
                    <div class="card card-outline {{ $isLocked ? 'card-danger' : 'card-success' }}">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                Grading: <strong>{{ $selectedSection->name }}</strong> |
                                <strong>{{ $selectedSubject->name }}</strong>
                                @if ($isLocked)
                                    <span class="badge badge-danger ml-2"><i class="fas fa-lock"></i> Locked</span>
                                @endif
                            </h3>
                        </div>

                        <form action="{{ resolveRoute('grades.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="section_id" value="{{ $selectedSection->id }}">
                            <input type="hidden" name="subject_id" value="{{ $selectedSubject->id }}">

                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover table-bordered table-striped m-0" id="gradebookTable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Student Name</th>
                                            @foreach ($weights as $weight)
                                                <th class="text-center text-nowrap">
                                                    {{ $weight->name }} <br>
                                                    <small class="text-muted">(Max: {{ $weight->weight }})</small>
                                                </th>
                                            @endforeach
                                            <th class="text-center bg-dark text-white text-nowrap">Total (100)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($students as $index => $student)
                                            @php
                                                $record = $existingGrades[$student->id] ?? null;
                                                $scores = $record ? $record->scores : [];
                                                $total = $record ? $record->total_score : 0;
                                            @endphp
                                            <tr data-student="{{ $student->id }}">
                                                <td class="align-middle">{{ $index + 1 }}</td>
                                                <td class="align-middle font-weight-bold">{{ $student->name }}</td>

                                                @foreach ($weights as $weight)
                                                    <td>
                                                        <input type="number"
                                                            name="grades[{{ $student->id }}][{{ $weight->id }}]"
                                                            class="form-control text-center score-input"
                                                            value="{{ $scores[$weight->id] ?? '' }}" min="0"
                                                            max="{{ $weight->weight }}" step="0.01"
                                                            {{ $isLocked ? 'readonly' : '' }}>
                                                    </td>
                                                @endforeach

                                                <td
                                                    class="align-middle text-center bg-light font-weight-bold total-display">
                                                    {{ $total }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ $weights->count() + 3 }}" class="text-center p-4">No
                                                    students found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if (!$isLocked && $students->count() > 0)
                                <div class="card-footer bg-light d-flex flex-column flex-sm-row justify-content-end">
                                    <button type="submit" class="btn btn-outline-primary mb-2 mb-sm-0 mr-sm-2">
                                        Save Draft
                                    </button>
                                    <button type="submit" name="publish_grades" value="1" class="btn btn-success"
                                        onclick="return confirm('Are you sure? Publishing will lock these grades and they cannot be edited.')">
                                        Publish & Lock Grades
                                    </button>
                                </div>
                            @endif
                        </form>
                    </div>
                @endif

            </div>
        </section>
    </div>

    <style>
        .score-input {
            min-width: 72px;
            min-height: 38px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.getElementById('gradebookTable');

            if (table) {
                table.addEventListener('input', function(e) {
                    if (e.target.classList.contains('score-input')) {
                        const row = e.target.closest('tr');
                        const inputs = row.querySelectorAll('.score-input');
                        let total = 0;

                        inputs.forEach(input => {
                            let val = parseFloat(input.value) || 0;
                            // Prevent typing a number higher than the max weight
                            let max = parseFloat(input.getAttribute('max'));
                            if (val > max) {
                                input.value = max;
                                val = max;
                            }
                            total += val;
                        });

                        row.querySelector('.total-display').innerText = total.toFixed(2);
                    }
                });
            }
        });
    </script>
@endsection
