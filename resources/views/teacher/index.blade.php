@extends('layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 mb-2">
                        <h1 class="m-0">Teacher Directory</h1>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex flex-wrap justify-content-sm-end">
                            <a href="{{ route('teachers.assignments') }}" class="btn btn-success mr-1 mr-sm-2 mb-1">
                                Assign to subjects
                            </a>
                            <a href="{{ route('teachers.create') }}" class="btn btn-primary mb-1">
                                Add New Teacher
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-nowrap">
                            <h3 class="card-title mb-2 md-sm-0 mr-2">
                                All Teachers
                                <span class="badge badge-secondary">{{ $teachers->count() }}</span>
                            </h3>
                            <x-search-filter :route="route('teachers.index')" placeholder="Search by name, email or employee ID..."
                                                :show-gender="true" />
                        </div>
                    </div>


                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped text-nowrap">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Qualification</th>
                                    <th>Assignment</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teachers as $teacher)
                                    <tr>
                                        <td class="align-middle">
                                            <span class="badge badge-secondary">
                                                {{ $teacher->teacherProfile->employee_id ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $teacher->teacherProfile->profile_picture
                                                    ? asset('storage/' . $teacher->teacherProfile->profile_picture)
                                                    : asset('dist/img/user2-160x160.jpg') }}"
                                                    class="img-circle mr-2"
                                                    style="width: 35px; height: 35px; object-fit: cover;" alt="Profile">
                                                <div>
                                                    <div class="font-weight-bold">{{ $teacher->name }}</div>
                                                    <div class="text-muted text-sm">{{ $teacher->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            {{ $teacher->teacherProfile->phone ?? 'N/A' }}
                                        </td>
                                        <td class="align-middle">
                                            {{ $teacher->teacherProfile->qualification ?? 'N/A' }}
                                        </td>
                                        <td class="align-middle text-wrap">
                                            @if ($teacher->allocations->count() > 0)
                                                @foreach ($teacher->allocations as $allocation)
                                                    <div class="mb-1">
                                                        <span class="badge badge-secondary">
                                                            {{ $allocation->subject->name }}
                                                        </span>
                                                        <small class="text-muted d-none d-md-inline">
                                                            {{ $allocation->section->classLevel->name ?? '' }}
                                                            - {{ $allocation->section->name ?? '' }}
                                                        </small>
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="badge badge-warning">Unassigned</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-right">
                                            <a href="{{ route('teachers.edit', $teacher->id) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Remove {{ $teacher->name }}? This cannot be undone.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            @if(request('search'))
                                                <i class="fas fa-search fa-2x mb-2"></i><br>
                                                No teachers found matching "{{ request('search') }}".
                                                <a href="{{ route('teachers.index') }}">Clear search</a>
                                            @else
                                                No teachers added yet.
                                            <a href="{{ route('teachers.create') }}">Add your first teacher.</a>
                                            @endif
                                        </td>
                                    </tr>

                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
