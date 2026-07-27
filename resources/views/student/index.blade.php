@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Student List</h1>
                    </div>
                    <div class="col-sm-6 text-right">
                        <div class="d-flex flex-wrap justify-content-sm-end">
                            <button class="btn btn-success mb-1 mx-1" data-toggle="modal" data-target="#modal-import">
                                Import Students
                            </button>
                            <a href="{{ route('students.export') }}" class="btn btn-info mb-1 mx-1">
                                Export Students
                            </a>
                            <a href="{{ route('student.create') }}" class="btn btn-primary mb-1 mx-1">
                                <i class="fas fa-plus"></i> New Admission
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                {{-- Search & Filter Component --}}
                {{-- <x-search-filter
                    :route="route('student.index')"
                    placeholder="Search by name or admission number..."
                    :show-class="true"
                    :show-section="true"
                    :show-gender="true"
                    :class-levels="$classLevels"
                    :sections="$sections"
                /> --}}

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-nowrap">
                            <h3 class="card-title mb-2 mb-md-0">
                                All Students
                                {{-- Show result count when filtering --}}
                                @if (request()->hasAny(['search', 'class_id', 'section_id', 'gender']))
                                    <span class="badge badge-info">
                                        {{ $students->total() }} found
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        {{ $students->total() }}
                                    </span>
                                @endif
                            </h3>
                            <x-search-filter :route="route('student.index')" placeholder="Search by name/adm no..." />
                        </div>
                    </div>

                    <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Admission No</th>
                                    <th>Class Info</th>
                                    <th>Parent / Guardian</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    <tr id="row-{{ $student->id }}">
                                        <td>
                                            <div class="user-block">
                                                <img class="img-circle img-bordered-sm"
                                                    src="{{ asset('dist/img/user2-160x160.jpg') }}" alt="User Image">
                                                <span class="username">
                                                    <a href="#">{{ $student->name }}</a>
                                                </span>
                                                <span class="description">
                                                    {{ $student->email ?? 'No Email' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-warning">
                                                {{ $student->studentProfile->admission_number ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($student->studentProfile && $student->studentProfile->section)
                                                <strong>
                                                    {{ $student->studentProfile->section->classLevel->name }}
                                                </strong><br>
                                                <small class="text-muted">
                                                    Section: {{ $student->studentProfile->section->name }}
                                                </small>
                                            @else
                                                <span class="text-danger">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($student->parents->isNotEmpty())
                                                {{ $student->parents->first()->name }}<br>
                                                <small class="text-muted">
                                                    {{ $student->parents->first()->pivot->relationship }} —
                                                    {{ $student->parents->first()->email }}
                                                </small>
                                            @else
                                                <span class="text-muted">No Parent Linked</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('student.show', $student->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="deleteStudent({{ $student->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form id="delete-form-{{ $student->id }}"
                                                action="{{ route('student.destroy', $student->id) }}" method="POST"
                                                style="display:none;">
                                                @csrf @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            @if (request()->hasAny(['search', 'class_id', 'section_id', 'gender']))
                                                <i class="fas fa-search fa-2x mb-2"></i><br>
                                                No students found matching your filters.
                                                <a href="{{ route('student.index') }}">Clear filters</a>
                                            @else
                                                <i class="fas fa-users-slash fa-2x mb-2"></i><br>
                                                No students found. Click "New Admission" to start.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                            </div>
                    </div>

                    <div class="card-footer clearfix">
                        {{ $students->links('pagination::bootstrap-4') }}
                    </div>
                </div>

            </div>
        </section>
    </div>

    {{-- Import Modal --}}
    <div class="modal fade" id="modal-import">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Bulk Import Students</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group">
                            <label>Select CSV/Excel File</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="alert alert-info text-sm">
                            <strong>Required CSV Headers:</strong><br>
                            first_name, last_name, email, admission_number,
                            class_level_id, section_id, date_of_birth, gender, address
                            <br><br>
                            <em>Note: class_level_id and section_id must be numeric IDs.</em>
                        </div>
                        <a href="{{ route('students.template') }}" class="btn btn-xs btn-default">
                            Download Sample Template
                        </a>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Upload & Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        window.deleteStudent = async function(id) {
            if (!confirm(
                    'Are you sure? This will delete the student profile, academic history, and unlink parents.'))
                return;

            const form = document.getElementById(`delete-form-${id}`);
            const row = document.getElementById(`row-${id}`);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: new FormData(form)
                });

                const data = await response.json();

                if (response.ok) {
                    window.showFlash('success', data.message);
                    row.style.transition = "all 0.5s ease";
                    row.style.opacity = "0";
                    setTimeout(() => row.remove(), 500);
                } else {
                    window.showFlash('error', data.message);
                }
            } catch (error) {
                window.showFlash('error', 'System Error');
            }
        }
    </script>
@endpush
