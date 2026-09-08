@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid py-3">

                <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                    <div>
                        <h4 class="m-0 font-weight-bold text-dark">
                            Attendance
                        </h4>
                        <p class="text-muted mb-0">
                            Term summary and monthly breakdown for {{ $activeTerm?->name ?? 'the current term' }}
                        </p>
                    </div>
                    <div class="text-muted">
                        <i class="far fa-calendar-alt mr-1"></i>{{ now()->format('D, M d, Y') }}
                    </div>
                </div>

                @include('parent.partials.child-tabs', ['prefix' => 'att'])

                <div class="tab-content">
                    @foreach ($childrenData as $data)
                        @php $child = $data['student']; @endphp
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                             id="att-child-{{ $child->id }}" role="tabpanel">

                            <div class="card card-outline card-success shadow-sm">
                                <div class="card-header">
                                    <h3 class="card-title font-weight-bold">
                                        {{ $child->name }}'s Attendance
                                    </h3>
                                </div>
                                <div class="card-body">

                                    {{-- Today + Term Summary --}}
                                    <div class="row text-center mb-4">
                                        <div class="col-6 col-md-3 mb-3">
                                            <div class="stat-icon stat-icon-soft-success mx-auto mb-2" style="width: 40px; height: 40px;">
                                                <i class="fas fa-calendar-day"></i>
                                            </div>
                                            <div class="h4 font-weight-bold text-{{ $data['today']?->status == 'PRESENT' ? 'success' : ($data['today']?->status == 'ABSENT' ? 'danger' : 'secondary') }} mb-0">
                                                {{ $data['today']?->status ?? 'N/A' }}
                                            </div>
                                            <small class="text-muted">Today</small>
                                        </div>
                                        <div class="col-6 col-md-3 mb-3">
                                            <div class="h4 font-weight-bold text-success mb-0">{{ $data['summary']['present'] }}</div>
                                            <small class="text-muted">Present</small>
                                        </div>
                                        <div class="col-6 col-md-3 mb-3">
                                            <div class="h4 font-weight-bold text-danger mb-0">{{ $data['summary']['absent'] }}</div>
                                            <small class="text-muted">Absent</small>
                                        </div>
                                        <div class="col-6 col-md-3 mb-3">
                                            <div class="h4 font-weight-bold text-warning mb-0">{{ $data['summary']['late'] }}</div>
                                            <small class="text-muted">Late</small>
                                        </div>
                                    </div>

                                    <div class="text-center mb-4">
                                        <span class="badge badge-{{ $data['summary']['rate'] >= 75 ? 'success' : 'danger' }} px-3 py-2"
                                              style="border-radius: 20px;">
                                            {{ $data['summary']['rate'] }}% Term Attendance Rate
                                        </span>
                                    </div>

                                    {{-- Monthly Breakdown --}}
                                    <h6 class="font-weight-bold text-muted mb-3">
                                        Monthly Breakdown (Last 6 Months)
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered m-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Month</th>
                                                    <th class="text-center text-success">Present</th>
                                                    <th class="text-center text-danger">Absent</th>
                                                    <th class="text-center text-warning">Late</th>
                                                    <th class="text-center">Rate</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($data['monthly'] as $month)
                                                    <tr>
                                                        <td class="font-weight-bold">{{ $month['month'] }}</td>
                                                        <td class="text-center text-success">{{ $month['present'] }}</td>
                                                        <td class="text-center text-danger">{{ $month['absent'] }}</td>
                                                        <td class="text-center text-warning">{{ $month['late'] }}</td>
                                                        <td class="text-center">
                                                            <span class="badge badge-{{ $month['rate'] >= 75 ? 'success' : 'danger' }}">
                                                                {{ $month['rate'] }}%
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center p-4 text-muted">
                                                            No attendance records found.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </section>
    </div>
@endsection
