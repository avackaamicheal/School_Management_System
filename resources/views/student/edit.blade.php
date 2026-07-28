@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Student</h1>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('student.index') }}" class="btn btn-outline-secondary float-right">
                            <i class="fas fa-arrow-left"></i> Back to Students
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                @php
                    $nameParts = explode(' ', $student->name, 2);
                    $firstName = $nameParts[0] ?? '';
                    $lastName = $nameParts[1] ?? '';
                @endphp

                <form id="edit-form" onsubmit="handleEdit(event)">
                    @csrf
                    @method('PUT')
                    <div class="row">

                        <div class="col-md-6">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Student Details</h3>
                                </div>
                                <div class="card-body">

                                    <div class="form-group row">
                                        <div class="col-md-6 col-12">
                                            <label>First Name <span class="text-danger">*</span></label>
                                            <input type="text" name="first_name" class="form-control"
                                                placeholder="e.g. John" value="{{ $firstName }}" required>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <label>Last Name <span class="text-danger">*</span></label>
                                            <input type="text" name="last_name" class="form-control"
                                                placeholder="e.g. Doe" value="{{ $lastName }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Email Address <small class="text-muted">(optional)</small></label>
                                        <input type="email" name="email" class="form-control"
                                            placeholder="student@email.com" value="{{ $student->email }}">
                                    </div>

                                    <div class="form-group">
                                        <label>Admission Number</label>
                                        <input type="text" class="form-control bg-light font-weight-bold"
                                            value="{{ $student->studentProfile->admission_number ?? 'N/A' }}" readonly>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-6 col-12">
                                            <label>Class Level <span class="text-danger">*</span></label>
                                            <select name="class_level_id" id="class_select" class="form-control"
                                                onchange="filterSections()" required>
                                                <option value="">-- Select Class --</option>
                                                @foreach ($classLevels as $class)
                                                    <option value="{{ $class->id }}"
                                                        data-sections="{{ $class->sections->toJson() }}"
                                                        {{ $student->studentProfile->class_level_id == $class->id ? 'selected' : '' }}>
                                                        {{ $class->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <label>Section <span class="text-danger">*</span></label>
                                            <select name="section_id" id="section_select" class="form-control" required>
                                                <option value="">-- Select Class First --</option>
                                                @if ($student->studentProfile->section)
                                                    <option value="{{ $student->studentProfile->section_id }}" selected>
                                                        {{ $student->studentProfile->section->name }}
                                                    </option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-6 col-12">
                                            <label>Date of Birth <span class="text-danger">*</span></label>
                                            <input type="date" name="dob" class="form-control" required
                                                value="{{ $student->studentProfile->date_of_birth }}">
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <label>Gender <span class="text-danger">*</span></label>
                                            <select name="gender" class="form-control" required>
                                                <option value="">-- Select --</option>
                                                <option value="Male" {{ $student->studentProfile->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ $student->studentProfile->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Address</label>
                                        <textarea name="address" class="form-control" rows="2" placeholder="Student home address">{{ $student->studentProfile->address }}</textarea>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <button type="submit" id="submitBtn" class="btn btn-warning btn-block btn-lg">
                                        <i class="fas fa-save"></i> Update Student
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>

            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        function filterSections() {
            let classSelect = document.getElementById('class_select');
            let sectionSelect = document.getElementById('section_select');
            let selectedOption = classSelect.options[classSelect.selectedIndex];
            let sections = selectedOption.getAttribute('data-sections');

            let currentSectionId = "{{ $student->studentProfile->section_id }}";

            sectionSelect.innerHTML = '<option value="">-- Select Section --</option>';

            if (sections) {
                JSON.parse(sections).forEach(function(section) {
                    let selected = section.id == currentSectionId ? 'selected' : '';
                    sectionSelect.innerHTML += `<option value="${section.id}" ${selected}>${section.name}</option>`;
                });
            }
        }

        window.handleEdit = async function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            let formData = new FormData(e.target);

            try {
                let response = await fetch("{{ route('student.update', $student->id) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                let data = await response.json();

                if (response.ok) {
                    window.showFlash('success', data.message);
                    setTimeout(() => window.location.href = "{{ route('student.show', $student->id) }}", 1500);
                } else if (response.status === 422) {
                    let errors = Object.values(data.errors).flat().join('\n');
                    alert('Validation Error:\n' + errors);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save"></i> Update Student';
                } else {
                    alert('Error: ' + data.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save"></i> Update Student';
                }
            } catch (error) {
                alert('System Error. Please try again.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Update Student';
            }
        }
    </script>
@endpush
