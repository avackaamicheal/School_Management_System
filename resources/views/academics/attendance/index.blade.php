@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0">Daily Attendance</h1>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                <div class="card card-default">
                    <div class="card-body">
                        <form action="{{ route($routes['index']) }}" method="GET" class="form-row align-items-end">
                            <div class="col-12 col-md-auto mb-2 mb-md-0">
                                <label class="mb-1">Class Section:</label>
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

                            <div class="col-12 col-md-auto mb-2 mb-md-0">
                                <label class="mb-1">Date:</label>
                                <input type="date" name="date" class="form-control" value="{{ $date }}"
                                    max="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="col-12 col-md-auto mb-2 mb-md-0">
                                <button type="submit" class="btn btn-primary d-block d-md-inline-block">Load Register</button>
                            </div>
                        </form>
                    </div>
                </div>

                @if ($selectedSection)
                    <div class="card card-primary card-outline">

                        <div class="card-header">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                <h3 class="card-title mb-2 mb-md-0">
                                    Marking Attendance for: <strong>{{ $selectedSection->classLevel->name }} -
                                        {{ $selectedSection->name }}</strong> on
                                    <strong>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</strong>
                                </h3>
                                <div class="card-tools">
                                    <a href="{{ route($routes['export'], ['section_id' => $selectedSection->id, 'date' => $date]) }}"
                                        class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-file-excel"></i> Export
                                    </a>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route($routes['store']) }}" method="POST">
                            @csrf
                            <input type="hidden" name="section_id" value="{{ $selectedSection->id }}">
                            <input type="hidden" name="date" value="{{ $date }}">

                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th class="d-none d-sm-table-cell">Admission No</th>
                                            <th>Student Name</th>
                                            <th>Attendance Status</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($students as $index => $student)
                                            @php
                                                $currentStatus = isset($attendances[$student->id])
                                                    ? $attendances[$student->id]->status
                                                    : 'PRESENT';
                                                $currentRemark = isset($attendances[$student->id])
                                                    ? $attendances[$student->id]->remarks
                                                    : '';
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td class="d-none d-sm-table-cell">{{ $student->studentProfile->admission_number ?? 'N/A' }}</td>
                                                <td class="font-weight-bold">{{ $student->name }}</td>
                                                <td>
                                                    <div class="d-flex flex-wrap">
                                                        <div class="icheck-success mr-3 mb-1">
                                                            <input type="radio" name="attendance[{{ $student->id }}]"
                                                                id="present_{{ $student->id }}" value="PRESENT"
                                                                {{ $currentStatus == 'PRESENT' ? 'checked' : '' }}>
                                                            <label for="present_{{ $student->id }}">Present</label>
                                                        </div>
                                                        <div class="icheck-danger mr-3 mb-1">
                                                            <input type="radio" name="attendance[{{ $student->id }}]"
                                                                id="absent_{{ $student->id }}" value="ABSENT"
                                                                {{ $currentStatus == 'ABSENT' ? 'checked' : '' }}>
                                                            <label for="absent_{{ $student->id }}">Absent</label>
                                                        </div>
                                                        <div class="icheck-warning mb-1">
                                                            <input type="radio" name="attendance[{{ $student->id }}]"
                                                                id="late_{{ $student->id }}" value="LATE"
                                                                {{ $currentStatus == 'LATE' ? 'checked' : '' }}>
                                                            <label for="late_{{ $student->id }}">Late</label>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" name="remarks[{{ $student->id }}]"
                                                        class="form-control form-control-sm"
                                                        placeholder="e.g. Sick" value="{{ $currentRemark }}">
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted p-4">No students found in
                                                    this class section.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if ($students->count() > 0)
                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-success btn-lg d-block d-md-inline-block">
                                        Save Attendance
                                    </button>
                                </div>
                            @endif
                        </form>

                    </div>
                @endif

            </div>
        </section>
    </div>
@endsection
