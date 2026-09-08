@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid py-3">

                <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                    <div>
                        <h4 class="m-0 font-weight-bold text-dark">
                            Our Teachers
                        </h4>
                        <p class="text-muted mb-0">
                            {{ $teachers->count() }} teacher{{ $teachers->count() == 1 ? '' : 's' }} on the school staff
                        </p>
                    </div>
                    <div class="text-muted">
                        <i class="far fa-calendar-alt mr-1"></i>{{ now()->format('D, M d, Y') }}
                    </div>
                </div>

                <div class="row">
                    @forelse($teachers as $teacher)
                        @php
                            $subjects = $teacher->allocations->pluck('subject.name')->filter()->unique();
                            $classes = $teacher->allocations->pluck('section.classLevel.name')->filter()->unique();
                        @endphp
                        <div class="col-12 col-sm-6 col-xl-4 mb-3">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        @if ($teacher->teacherProfile?->profile_picture)
                                            <img src="{{ asset('storage/' . $teacher->teacherProfile->profile_picture) }}"
                                                 alt="{{ $teacher->name }}"
                                                 class="img-circle mr-3"
                                                 style="width: 52px; height: 52px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3"
                                                style="width: 52px; height: 52px; font-size: 1.3rem; font-weight: 800; flex-shrink: 0;">
                                                {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <h5 class="mb-0 font-weight-bold">{{ $teacher->name }}</h5>
                                            @if ($teacher->teacherProfile?->qualification)
                                                <small class="text-muted">
                                                    <i class="fas fa-graduation-cap mr-1"></i>
                                                    {{ $teacher->teacherProfile->qualification }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($subjects->count() > 0)
                                        <div class="mb-1">
                                            <small class="text-muted font-weight-bold d-block mb-1">Subjects</small>
                                            @foreach ($subjects as $subject)
                                                <span class="badge badge-primary mr-1 mb-1">{{ $subject }}</span>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if ($classes->count() > 0)
                                        <div class="mb-3">
                                            <small class="text-muted font-weight-bold d-block mb-1">Classes</small>
                                            @foreach ($classes as $class)
                                                <span class="badge badge-info mr-1 mb-1">{{ $class }}</span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <form action="{{ resolveRoute('messages.thread.create') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $teacher->id }}">
                                        <button type="submit" class="btn btn-sm btn-outline-success btn-block text-left">
                                            <i class="fas fa-envelope mr-2"></i> Send Message
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-body text-center text-muted p-5">
                                    <i class="fas fa-chalkboard-teacher fa-3x mb-3 d-block"></i>
                                    No teachers found at this school yet.
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

            </div>
        </section>
    </div>
@endsection
