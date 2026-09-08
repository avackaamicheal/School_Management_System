@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid py-3">

                <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                    <div>
                        <h4 class="m-0 font-weight-bold text-dark">
                            Results / Report Card
                        </h4>
                        <p class="text-muted mb-0">
                            Published grades for {{ $activeTerm?->name ?? 'the current term' }}
                        </p>
                    </div>
                    <div class="text-muted">
                        <i class="far fa-calendar-alt mr-1"></i>{{ now()->format('D, M d, Y') }}
                    </div>
                </div>

                @include('parent.partials.child-tabs', ['prefix' => 'res'])

                <div class="tab-content">
                    @foreach ($childrenData as $data)
                        @php $child = $data['student']; @endphp
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                             id="res-child-{{ $child->id }}" role="tabpanel">

                            <div class="card card-outline card-info shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                                    <h3 class="card-title font-weight-bold">
                                        <i class="fas fa-chart-line mr-1 text-info"></i>
                                        {{ $child->name }}'s Grades
                                    </h3>
                                    <a href="{{ resolveRoute('reports.single', $child->id) }}"
                                       class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-file-pdf"></i> Download Report Card
                                    </a>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover m-0">
                                            <thead>
                                                <tr>
                                                    <th>Subject</th>
                                                    <th class="text-center">Score</th>
                                                    <th class="text-center">Grade</th>
                                                    <th class="text-center">Remark</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($data['grades'] as $grade)
                                                    @php
                                                        $score = $grade->total_score;
                                                        $letter = $score >= 70 ? 'A' : ($score >= 60 ? 'B' : ($score >= 50 ? 'C' : ($score >= 40 ? 'D' : 'F')));
                                                        $remark = $score >= 70 ? 'Excellent' : ($score >= 60 ? 'Very Good' : ($score >= 50 ? 'Good' : ($score >= 40 ? 'Pass' : 'Needs Improvement')));
                                                        $badgeClass = $score >= 70 ? 'success' : ($score >= 50 ? 'warning' : 'danger');
                                                    @endphp
                                                    <tr>
                                                        <td class="align-middle font-weight-bold">
                                                            {{ $grade->subject->name }}
                                                        </td>
                                                        <td class="align-middle text-center">{{ $score }}%</td>
                                                        <td class="align-middle text-center">
                                                            <span class="badge badge-{{ $badgeClass }} p-2">{{ $letter }}</span>
                                                        </td>
                                                        <td class="align-middle text-center">{{ $remark }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center p-4 text-muted">
                                                            <i class="fas fa-chart-bar fa-2x mb-2 d-block"></i>
                                                            No published grades yet for this term.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @if ($data['grades']->count() > 0)
                                    <div class="card-footer">
                                        <span class="font-weight-bold">
                                            Term Average:
                                            <strong class="text-primary">{{ $data['average'] }}%</strong>
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </section>
    </div>
@endsection
