@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Class Assignments</h1></div>
                <div class="col-sm-6 text-left text-md-right mt-2">
                    <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Teachers
                    </a>
                    <button class="btn btn-primary" onclick="openCreateModal()">
                            <i class="fas fa-plus"></i> New Assignment
                        </button>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between flex-nowrap">
                        <h3 class="card-title mb-2 mb-sm-0 mr-2">Teacher Assignments</h3>

                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped text-nowrap">
                        <thead>
                            <tr>
                                <th>Teacher</th>
                                <th>Subject</th>
                                <th>Class</th>
                                <th>Section</th>
                                <th class="text-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $hasAssignments = false; @endphp
                            @foreach($teachers as $teacher)
                                @foreach($teacher->allocations as $allocation)
                                    @php $hasAssignments = true; @endphp
                                    <tr>
                                        <td class="align-middle">
                                            <div class="font-weight-bold">{{ $teacher->name }}</div>
                                            <div class="text-muted text-sm">
                                                {{ $teacher->teacherProfile->employee_id ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="align-middle">{{ $allocation->subject->name }}</td>
                                        <td class="align-middle">{{ $allocation->section->classLevel->name ?? '' }}</td>
                                        <td class="align-middle">{{ $allocation->section->name ?? '' }}</td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center" style="gap: 0.25rem;">
                                                <button class="btn btn-info btn-sm"
                                                    onclick="openEditModal(
                                                        {{ $allocation->id }},
                                                        {{ $teacher->id }},
                                                        {{ $allocation->subject_id }},
                                                        {{ $allocation->section->classLevel->id ?? 'null' }},
                                                        {{ $allocation->section_id }}
                                                    )">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm"
                                                    onclick="handleDelete({{ $allocation->id }}, {{ $teacher->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <form id="delete-form-{{ $allocation->id }}-{{ $teacher->id }}"
                                                action="{{ route('teachers.allocations.destroy', [$teacher->id, $allocation->id]) }}"
                                                method="POST" style="display:none;">
                                                @csrf @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                            @if(!$hasAssignments)
                                <tr>
                                    <td colspan="5" class="text-center p-4">
                                        <i class="fas fa-puzzle-piece fa-2x mb-2"></i><br>
                                        No assignments found.
                                        <button class="btn btn-link p-0" onclick="openCreateModal()">Add an assignment</button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="modal-assignment">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-title">Add New Assignment</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="assignment-form" onsubmit="handleFormSubmit(event)">
                @csrf
                <input type="hidden" id="allocation_id" name="allocation_id">

                <div class="modal-body">
                    <div id="error-box" class="alert alert-danger d-none">
                        <ul id="error-list" class="mb-0 pl-3"></ul>
                    </div>

                    <div class="form-group">
                        <label for="teacher_id">Teacher</label>
                        <select name="teacher_id" id="teacher_id" class="form-control" required>
                            <option value="">-- Select Teacher --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="subject_id">Subject</label>
                        <select name="subject_id" id="subject_id" class="form-control" required>
                            <option value="">-- Subject --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="class_level_id">Class</label>
                        <select name="class_level_id" id="class_level_id" class="form-control" required>
                            <option value="">-- Class --</option>
                            @foreach($classLevels as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="section_id">Section</label>
                        <select name="section_id" id="section_id" class="form-control" required>
                            <option value="">-- Section --</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="save-btn">Assign Teacher</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        const sectionsByClass = {
            @foreach($classLevels as $class)
                "{{ $class->id }}": [
                    @foreach($class->sections as $section)
                        { id: "{{ $section->id }}", name: "{{ $section->name }}" },
                    @endforeach
                ],
            @endforeach
        };

        document.getElementById('class_level_id').addEventListener('change', function() {
            const classId = this.value;
            const sectionSelect = document.getElementById('section_id');
            sectionSelect.innerHTML = '<option value="">-- Section --</option>';

            if (classId && sectionsByClass[classId]) {
                sectionsByClass[classId].forEach(function(section) {
                    const option = document.createElement('option');
                    option.value = section.id;
                    option.textContent = section.name;
                    sectionSelect.appendChild(option);
                });
            }
        });

        function openCreateModal() {
            document.getElementById('assignment-form').reset();
            document.getElementById('allocation_id').value = '';
            document.getElementById('error-box').classList.add('d-none');
            document.getElementById('modal-title').innerText = 'Add New Assignment';
            document.getElementById('section_id').innerHTML = '<option value="">-- Section --</option>';
            $('#modal-assignment').modal('show');
        }

        function openEditModal(id, teacherId, subjectId, classLevelId, sectionId) {
            document.getElementById('assignment-form').reset();
            document.getElementById('allocation_id').value = id;
            document.getElementById('teacher_id').value = teacherId;
            document.getElementById('subject_id').value = subjectId;
            document.getElementById('class_level_id').value = classLevelId;

            document.getElementById('class_level_id').dispatchEvent(new Event('change'));

            setTimeout(function() {
                document.getElementById('section_id').value = sectionId;
            }, 0);

            document.getElementById('modal-title').innerText = 'Edit Assignment';
            document.getElementById('error-box').classList.add('d-none');
            $('#modal-assignment').modal('show');
        }

        window.handleFormSubmit = async function(e) {
            e.preventDefault();
            let form = e.target;
            let formData = new FormData(form);
            let id = document.getElementById('allocation_id').value;

            let url = "{{ route('assignments.store') }}";
            if (id) {
                let updateUrl = "{{ route('assignments.update', ':id') }}";
                url = updateUrl.replace(':id', id);
                formData.append('_method', 'PUT');
            }

            try {
                let response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                let data = await response.json();

                if (response.status === 422) {
                    let errorBox = document.getElementById('error-box');
                    let errorList = document.getElementById('error-list');
                    errorBox.classList.remove('d-none');
                    if (data.errors) {
                        errorList.innerHTML = Object.values(data.errors).flat().map(msg => `<li>${msg}</li>`).join('');
                    } else {
                        errorList.innerHTML = `<li>${data.message || 'Validation error'}</li>`;
                    }
                } else if (response.ok) {
                    $('#modal-assignment').modal('hide');
                    window.showFlash('success', data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    window.showFlash('error', 'Server Error: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                window.showFlash('error', 'Network Error: Check your connection.');
            }
        }

        window.handleDelete = async function(allocationId, teacherId) {
            if (!confirm('Are you sure you want to remove this assignment?')) return;

            const form = document.getElementById(`delete-form-${allocationId}-${teacherId}`);
            const url = form.action;
            const formData = new FormData(form);

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    window.showFlash('success', data.message || 'Assignment removed successfully.');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    window.showFlash('error', 'Could not delete: ' + (data.message || 'Server error'));
                }
            } catch (error) {
                window.showFlash('error', 'System error occurred during deletion.');
            }
        }
    </script>
@endpush
