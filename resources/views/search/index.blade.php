@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">
                Search Results
                @if($query)
                    for <strong>"{{ $query }}"</strong>
                    @isset($totalResults)
                        <span class="badge badge-info ml-2">{{ $totalResults }} results</span>
                    @endisset
                @endif
            </h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            {{-- Search & Filter Form --}}
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <form action="{{ route('search.index') }}" method="GET">
                        <div class="row">
                            {{-- Search Input --}}
                            <div class="col-12 col-md-6 col-lg-2">
                                <div class="input-group">
                                    <input type="text" name="q" class="form-control"
                                        placeholder="Search..."
                                        value="{{ $query }}" autofocus>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Type Filter --}}
                            <div class="col-12 col-md-6 col-lg-2">
                                <select name="type" class="form-control">
                                    <option value="all" {{ $type == 'all' ? 'selected' : '' }}>
                                        All Types
                                    </option>
                                    <option value="students" {{ $type == 'students' ? 'selected' : '' }}>
                                        Students
                                    </option>
                                    <option value="teachers" {{ $type == 'teachers' ? 'selected' : '' }}>
                                        Teachers
                                    </option>
                                    <option value="invoices" {{ $type == 'invoices' ? 'selected' : '' }}>
                                        Invoices
                                    </option>
                                    <option value="grades" {{ $type == 'grades' ? 'selected' : '' }}>
                                        Grades
                                    </option>
                                </select>
                            </div>

                            {{-- Class Filter --}}
                            <div class="col-12 col-md-4 col-lg-2">
                                <select name="class_id" class="form-control"
                                    onchange="this.form.submit()">
                                    <option value="">Classes</option>
                                    @foreach($classLevels as $class)
                                        <option value="{{ $class->id }}"
                                            {{ $classId == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Section Filter --}}
                            <div class="col-12 col-md-4 col-lg-2">
                                <select name="section_id" class="form-control">
                                    <option value="">Sections</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}"
                                            {{ $sectionId == $section->id ? 'selected' : '' }}>
                                            {{ $section->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Gender Filter --}}
                            <div class="col-12 col-md-4 col-lg-2">
                                <select name="gender" class="form-control">
                                    <option value="">Gender</option>
                                    <option value="Male" {{ $gender == 'Male' ? 'selected' : '' }}>
                                        Male
                                    </option>
                                    <option value="Female" {{ $gender == 'Female' ? 'selected' : '' }}>
                                        Female
                                    </option>
                                </select>
                            </div>

                            {{-- Status Filter --}}
                            <div class="col-12 col-md-4 col-lg-2">
                                <select name="status" class="form-control">
                                    <option value="">Status</option>
                                    <option value="PAID" {{ $status == 'PAID' ? 'selected' : '' }}>
                                        Paid
                                    </option>
                                    <option value="UNPAID" {{ $status == 'UNPAID' ? 'selected' : '' }}>
                                        Unpaid
                                    </option>
                                    <option value="PARTIAL" {{ $status == 'PARTIAL' ? 'selected' : '' }}>
                                        Partial
                                    </option>
                                    <option value="published" {{ $status == 'published' ? 'selected' : '' }}>
                                        Published
                                    </option>
                                    <option value="draft" {{ $status == 'draft' ? 'selected' : '' }}>
                                        Draft
                                    </option>
                                </select>
                            </div>
                        </div>

                        @if($query || $classId || $sectionId || $gender || $status)
                            <div class="mt-2">
                                <a href="{{ route('search.index') }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-times"></i> Clear Filters
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            @if(!$query)
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-search fa-4x mb-3"></i>
                    <h4>Type something to search</h4>
                    <p>Search across students, teachers, invoices and grades</p>
                </div>
            @else

                {{-- Students Results --}}
                @if($results['students']->count() > 0)
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user-graduate mr-1 text-info"></i>
                                Students
                                <span class="badge badge-info ml-2">
                                    {{ $results['students']->count() }}
                                </span>
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('student.index') }}" class="btn btn-sm btn-outline-info">
                                    View All Students
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-striped m-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Admission No</th>
                                        <th>Class</th>
                                        <th>Section</th>
                                        <th>Gender</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results['students'] as $student)
                                        <tr>
                                            <td class="align-middle font-weight-bold">
                                                {{ $student->name }}
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge badge-secondary">
                                                    {{ $student->studentProfile->admission_number ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                {{ $student->studentProfile->section->classLevel->name ?? 'N/A' }}
                                            </td>
                                            <td class="align-middle">
                                                {{ $student->studentProfile->section->name ?? 'N/A' }}
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge badge-{{ $student->studentProfile->gender == 'Male' ? 'info' : 'danger' }}">
                                                    {{ $student->studentProfile->gender ?? 'N/A' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Teachers Results --}}
                @if($results['teachers']->count() > 0)
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chalkboard-teacher mr-1 text-success"></i>
                                Teachers
                                <span class="badge badge-success ml-2">
                                    {{ $results['teachers']->count() }}
                                </span>
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('teachers.index') }}" class="btn btn-sm btn-outline-success">
                                    View All Teachers
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-striped m-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Employee ID</th>
                                        <th>Email</th>
                                        <th>Qualification</th>
                                        <th>Assignments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results['teachers'] as $teacher)
                                        <tr>
                                            <td class="align-middle font-weight-bold">
                                                {{ $teacher->name }}
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge badge-secondary">
                                                    {{ $teacher->teacherProfile->employee_id ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="align-middle">{{ $teacher->email }}</td>
                                            <td class="align-middle">
                                                {{ $teacher->teacherProfile->qualification ?? 'N/A' }}
                                            </td>
                                            <td class="align-middle">
                                                @foreach($teacher->allocations->take(2) as $allocation)
                                                    <span class="badge badge-info mr-1">
                                                        {{ $allocation->subject->name }}
                                                    </span>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Invoices Results --}}
                @if($results['invoices']->count() > 0)
                    <div class="card card-outline card-warning">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-invoice-dollar mr-1 text-warning"></i>
                                Invoices
                                <span class="badge badge-warning ml-2">
                                    {{ $results['invoices']->count() }}
                                </span>
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-warning">
                                    View All Invoices
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-striped m-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Student</th>
                                        <th>Total</th>
                                        <th class="d-none d-md-table-cell">Paid</th>
                                        <th class="d-none d-md-table-cell">Balance</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results['invoices'] as $invoice)
                                        @php
                                            $paid    = $invoice->payments_sum_amount ?? 0;
                                            $balance = $invoice->total_amount - $paid;
                                        @endphp
                                        <tr>
                                            <td class="align-middle font-weight-bold">
                                                {{ $invoice->invoice_number }}
                                            </td>
                                            <td class="align-middle">
                                                {{ $invoice->student->name ?? 'N/A' }}
                                            </td>
                                            <td class="align-middle">
                                                ₦{{ number_format($invoice->total_amount) }}
                                            </td>
                                            <td class="align-middle text-success d-none d-md-table-cell">
                                                ₦{{ number_format($paid) }}
                                            </td>
                                            <td class="align-middle text-danger d-none d-md-table-cell">
                                                ₦{{ number_format($balance) }}
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge badge-{{ $invoice->status == 'PAID' ? 'success' : ($invoice->status == 'PARTIAL' ? 'warning' : 'danger') }}">
                                                    {{ $invoice->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Grades Results --}}
                @if($results['grades']->count() > 0)
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-graduation-cap mr-1 text-primary"></i>
                                Grades
                                <span class="badge badge-primary ml-2">
                                    {{ $results['grades']->count() }}
                                </span>
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.grades.index') }}" class="btn btn-sm btn-outline-primary">
                                    View All Grades
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-striped m-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Student</th>
                                        <th>Subject</th>
                                        <th>Score</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results['grades'] as $grade)
                                        <tr>
                                            <td class="align-middle font-weight-bold">
                                                {{ $grade->student->name ?? 'N/A' }}
                                            </td>
                                            <td class="align-middle">
                                                {{ $grade->subject->name ?? 'N/A' }}
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge badge-primary p-2">
                                                    {{ $grade->total_score }}%
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                @if($grade->is_locked)
                                                    <span class="badge badge-success">Published</span>
                                                @else
                                                    <span class="badge badge-warning">Draft</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- No results --}}
                @if(isset($totalResults) && $totalResults === 0)
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-search fa-3x mb-3"></i>
                        <h4>No results found for "{{ $query }}"</h4>
                        <p>Try a different search term or adjust your filters.</p>
                    </div>
                @endif

            @endif

        </div>
    </section>
</div>
@endsection
