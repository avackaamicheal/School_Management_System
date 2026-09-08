@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid py-3">

                <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                    <div>
                        <h4 class="m-0 font-weight-bold text-dark">
                            My Children
                        </h4>
                        <p class="text-muted mb-0">All students linked to your family account</p>
                    </div>
                    <div class="text-muted">
                        <i class="far fa-calendar-alt mr-1"></i>{{ now()->format('D, M d, Y') }}
                    </div>
                </div>

                <div class="row">
                    @foreach ($childrenData as $data)
                        @php $child = $data['student']; @endphp
                        <div class="col-12 col-md-6 col-xl-4 mb-3">
                            <div class="card shadow-sm h-100">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center mr-3"
                                            style="width: 52px; height: 52px; font-size: 1.3rem; font-weight: 800;
                                                   background: linear-gradient(135deg, #4f46e5, #7c3aed); flex-shrink: 0;">
                                            {{ strtoupper(substr($child->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h5 class="mb-0 font-weight-bold">{{ $child->name }}</h5>
                                            <small class="text-muted">
                                                {{ $data['classLevel']->name ?? 'N/A' }} {{ $data['section']->name ?? 'N/A' }}
                                            </small>
                                        </div>
                                    </div>

                                    <div class="text-muted text-sm mb-3">
                                        <i class="fas fa-id-card mr-1"></i>
                                        Admission: {{ $child->studentProfile->admission_number ?? 'N/A' }}
                                    </div>

                                    <div class="row text-center mb-3">
                                        <div class="col-6">
                                            <div class="font-weight-bold {{ $data['outstandingBalance'] > 0 ? 'text-danger' : 'text-success' }}">
                                                ₦{{ number_format($data['outstandingBalance']) }}
                                            </div>
                                            <small class="text-muted">Outstanding</small>
                                        </div>
                                        <div class="col-6">
                                            <div class="font-weight-bold text-primary">
                                                {{ $data['average'] ?? 'N/A' }}{{ $data['average'] ? '%' : '' }}
                                            </div>
                                            <small class="text-muted">Term Average</small>
                                        </div>
                                    </div>

                                    <div class="mt-auto">
                                        <a href="{{ route('parent.results.index') }}"
                                            class="btn btn-sm btn-outline-info btn-block mb-2 text-left">
                                            <i class="fas fa-file-contract mr-2"></i> Results / Report Card
                                        </a>
                                        <a href="{{ route('parent.attendance.index') }}"
                                            class="btn btn-sm btn-outline-success btn-block mb-2 text-left">
                                            <i class="fas fa-user-check mr-2"></i> Attendance
                                        </a>
                                        <a href="{{ route('parent.timetable.index') }}"
                                            class="btn btn-sm btn-outline-primary btn-block mb-2 text-left">
                                            <i class="fas fa-calendar-week mr-2"></i> Time Table
                                        </a>
                                        <a href="{{ route('parent.fees.index') }}"
                                            class="btn btn-sm btn-outline-danger btn-block text-left">
                                            <i class="fas fa-file-invoice-dollar mr-2"></i> Fees & Payments
                                        </a>
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
