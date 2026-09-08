@extends('layouts.app')

@php $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']; @endphp

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid py-3">

                <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                    <div>
                        <h4 class="m-0 font-weight-bold text-dark">
                            Time Table
                        </h4>
                        <p class="text-muted mb-0">
                            Weekly class schedule for {{ $activeTerm?->name ?? 'the current term' }}
                        </p>
                    </div>
                    <div class="text-muted">
                        <i class="far fa-calendar-alt mr-1"></i>{{ now()->format('D, M d, Y') }}
                    </div>
                </div>

                @include('parent.partials.child-tabs', ['prefix' => 'tt'])

                <div class="tab-content">
                    @foreach ($childrenData as $data)
                        @php $child = $data['student']; @endphp
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                             id="tt-child-{{ $child->id }}" role="tabpanel">

                            <div class="row">
                                @foreach ($days as $day)
                                    @php
                                        $isToday = now()->format('l') === $day;
                                        $daySlots = $data['slots'][$day] ?? collect();
                                    @endphp
                                    <div class="col-12 col-sm-6 col-lg mb-2">
                                        <div class="card shadow-sm h-100 {{ $isToday ? 'card-outline card-primary' : '' }}">
                                            <div class="card-header py-2">
                                                <h5 class="card-title font-weight-bold mb-0">
                                                    {{ $day }}
                                                    @if ($isToday)
                                                        <span class="badge badge-primary ml-1">Today</span>
                                                    @endif
                                                </h5>
                                            </div>
                                            <div class="card-body p-0">
                                                @if ($daySlots->count() > 0)
                                                    <ul class="list-group list-group-flush">
                                                        @foreach ($daySlots as $slot)
                                                            <li class="list-group-item">
                                                                <div class="font-weight-bold text-sm">
                                                                    <i class="fas fa-clock text-muted mr-1"></i>
                                                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} -
                                                                    {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                                </div>
                                                                <div class="text-sm font-weight-bold">
                                                                    {{ $slot->subject->name }}
                                                                </div>
                                                                <small class="text-muted">
                                                                    <i class="fas fa-user-tie mr-1"></i>
                                                                    {{ $slot->teacher->name ?? 'TBA' }}
                                                                </small>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <div class="text-center p-3 text-muted text-sm">
                                                        <i class="fas fa-coffee mb-1 d-block"></i>
                                                        No classes
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    @endforeach
                </div>

            </div>
        </section>
    </div>
@endsection
