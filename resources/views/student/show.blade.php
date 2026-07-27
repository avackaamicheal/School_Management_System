@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <h1 class="m-0">
                    <a href="{{ route('student.index') }}" class="text-muted"><i class="fas fa-arrow-left"></i></a>
                    Student Profile
                </h1>
                {{-- <div>
                    <a href="{{ route('student.edit', $student->id) }}" class="btn btn-warning shadow-sm">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                </div> --}}
            </div>
        </div>

        <section class="content mt-3">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-md-4">
                        <div class="card card-primary card-outline shadow-sm">
                            <div class="card-body box-profile text-center">
                                <img class="profile-user-img img-fluid img-circle"
                                    src="{{ asset('dist/img/user1-128x128.jpg') }}" alt="User profile picture">
                                <h3 class="profile-username">{{ $student->name }}</h3>
                                <p class="text-muted">{{ $student->studentProfile->admission_number ?? 'No ID Assigned' }}</p>

                                <ul class="list-group list-group-unbordered mb-3 text-left mt-4">
                                    <li class="list-group-item">
                                        <b>Email</b>
                                        <a class="float-right text-dark">{{ $student->email ?? 'N/A' }}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Class</b>
                                        <a class="float-right text-dark">
                                            {{ $student->studentProfile->section->classLevel->name ?? '' }}
                                            - {{ $student->studentProfile->section->name ?? '' }}
                                        </a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Gender</b>
                                        <a class="float-right text-dark">{{ $student->studentProfile->gender ?? 'N/A' }}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Date of Birth</b>
                                        <a class="float-right text-dark">{{ $student->studentProfile->date_of_birth ?? 'N/A' }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        @if ($student->studentProfile->address)
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <h3 class="card-title font-weight-bold"><i class="fas fa-map-marker-alt text-danger mr-2"></i> Address</h3>
                                </div>
                                <div class="card-body text-sm">
                                    {{ $student->studentProfile->address }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-8">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h3 class="card-title font-weight-bold"><i class="fas fa-phone-alt text-success mr-2"></i> Parent / Guardian</h3>
                            </div>
                            <div class="card-body">
                                @if ($student->parents->isNotEmpty())
                                    @foreach ($student->parents as $parent)
                                        <div class="row mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                                            <div class="col-sm-6">
                                                <p class="mb-1 text-muted">Name</p>
                                                <h5>{{ $parent->name }}</h5>
                                            </div>
                                            <div class="col-sm-6">
                                                <p class="mb-1 text-muted">Email</p>
                                                <h5>{{ $parent->email }}</h5>
                                            </div>
                                            @if ($parent->pivot->relationship)
                                                <div class="col-12 mt-2">
                                                    <span class="badge badge-info">{{ $parent->pivot->relationship }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div class="alert alert-light text-muted border mb-0">
                                        <i class="fas fa-info-circle mr-2"></i> No parent linked to this student yet.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
@endsection
