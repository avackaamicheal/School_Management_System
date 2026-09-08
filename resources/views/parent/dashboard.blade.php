@extends('layouts.app')

@php
    $chartData = $childrenData->map(function ($d) {
        return [
            'id'             => (string) $d['student']->id,
            'present'        => $d['termPresent'],
            'absent'         => $d['termAbsent'],
            'late'           => max(0, $d['termTotal'] - $d['termPresent'] - $d['termAbsent']),
            'rate'           => $d['termRate'],
            'months'         => $d['monthlyBreakdown']->pluck('month'),
            'monthlyPresent' => $d['monthlyBreakdown']->pluck('present'),
            'monthlyAbsent'  => $d['monthlyBreakdown']->pluck('absent'),
            'monthlyLate'    => $d['monthlyBreakdown']->pluck('late'),
            'subjects'       => $d['grades']->pluck('subject.name'),
            'scores'         => $d['grades']->map(fn($g) => $g->total_score),
        ];
    })->keyBy('id');
@endphp

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid py-3">

                {{-- ============ WELCOME ROW ============ --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                    <div>
                        <p class="mb-1" style="font-size: 1.1rem;">
                            <strong>Welcome back, {{ $parent->name }}</strong>
                        </p>
                        @if ($children->count() > 1)
                            <small class="text-muted">
                                <i class="fas fa-users mr-1"></i>
                                {{ $children->count() }} Children Enrolled &nbsp;|&nbsp;
                                <strong class="text-danger">
                                    ₦{{ number_format($totalOutstanding) }}
                                </strong>
                                Total Outstanding Fees
                            </small>
                        @endif
                    </div>
                    <div class="text-right mt-2 mt-sm-0">
                        <div class="text-muted">
                            <i class="far fa-calendar-alt mr-1"></i>
                            {{ now()->format('D, M d, Y') }}
                        </div>
                    </div>
                </div>

                {{-- ============ CHILD SWITCHER TABS ============ --}}
                <ul class="nav nav-pills mb-4" id="childTabs" role="tablist"
                    style="flex-wrap: nowrap; overflow-x: auto; padding-bottom: 4px;">
                    @foreach ($childrenData as $data)
                        @php $child = $data['student']; @endphp
                        <li class="nav-item mr-2" role="presentation">
                            <a class="child-pill {{ $loop->first ? 'active' : '' }}"
                               id="child-tab-{{ $child->id }}"
                               data-toggle="tab" href="#child-{{ $child->id }}"
                               role="tab" aria-controls="child-{{ $child->id }}"
                               aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                <span class="pill-avatar">{{ strtoupper(substr($child->name, 0, 1)) }}</span>
                                <span>
                                    <span class="pill-name d-block">{{ $child->name }}</span>
                                    <span class="pill-class d-block">
                                        {{ $data['classLevel']->name ?? 'Class' }} {{ $data['section']->name ?? '' }}
                                    </span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content" id="childTabsContent">

                    @foreach ($childrenData as $data)
                        @php
                            $child = $data['student'];
                            $invoices = $data['invoices'];
                            $grades = $data['grades'];
                        @endphp

                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                             id="child-{{ $child->id }}" role="tabpanel"
                             aria-labelledby="child-tab-{{ $child->id }}">

                            {{-- Profile Strip --}}
                            <div class="profile-strip p-3 mb-3 d-flex align-items-center flex-wrap">
                                <div class="rounded-circle text-white d-flex align-items-center justify-content-center mr-3"
                                    style="width: 52px; height: 52px; font-size: 1.3rem; font-weight: 800;
                                           background: linear-gradient(135deg, #4f46e5, #7c3aed); flex-shrink: 0;">
                                    {{ strtoupper(substr($child->name, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="mb-0 font-weight-bold">{{ $child->name }}</h4>
                                    <small class="text-muted">
                                        <i class="fas fa-layer-group mr-1"></i>
                                        {{ $data['classLevel']->name ?? 'N/A' }} {{ $data['section']->name ?? 'N/A' }}
                                        <span class="mx-1">|</span>
                                        <i class="fas fa-id-card mr-1"></i>
                                        Admission: {{ $child->studentProfile->admission_number ?? 'N/A' }}
                                    </small>
                                </div>
                                <div class="ml-3">
                                    <a href="{{ resolveRoute('reports.single', $child->id) }}"
                                       class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-file-pdf mr-1"></i>
                                        <span class="d-none d-sm-inline">Report Card</span>
                                    </a>
                                </div>
                            </div>

                            {{-- ============ STAT TILES ============ --}}
                            <div class="row mb-4">
                                {{-- Fees --}}
                                <div class="col-12 col-sm-6 col-lg-3 mb-3">
                                    <div class="stat-tile">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="stat-icon {{ $data['outstandingBalance'] > 0 ? 'stat-icon-soft-danger' : 'stat-icon-soft-success' }} mr-3">
                                                <i class="fas fa-file-invoice-dollar"></i>
                                            </div>
                                            <div>
                                                <div class="stat-value {{ $data['outstandingBalance'] > 0 ? 'text-danger' : 'text-success' }}">
                                                    @if ($data['outstandingBalance'] > 0)
                                                        ₦{{ number_format($data['outstandingBalance']) }}
                                                    @else
                                                        <i class="fas fa-check-circle text-success"></i>
                                                    @endif
                                                </div>
                                                <div class="stat-label">
                                                    {{ $data['outstandingBalance'] > 0 ? 'Outstanding Fees' : 'Fees Cleared' }}
                                                </div>
                                            </div>
                                        </div>
                                        <a href="#fees-{{ $child->id }}"
                                           class="stat-foot {{ $data['outstandingBalance'] > 0 ? 'warn' : 'good' }}">
                                            {{ $data['outstandingBalance'] > 0 ? 'Pay Now' : 'View Receipts' }}
                                            <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>

                                {{-- Attendance Rate --}}
                                <div class="col-12 col-sm-6 col-lg-3 mb-3">
                                    <div class="stat-tile">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="stat-icon {{ $data['termRate'] >= 75 ? 'stat-icon-soft-info' : 'stat-icon-soft-warning' }} mr-3">
                                                <i class="fas fa-user-check"></i>
                                            </div>
                                            <div>
                                                <div class="stat-value">{{ $data['termRate'] }}%</div>
                                                <div class="stat-label">Term Attendance Rate</div>
                                            </div>
                                        </div>
                                        <a href="#attendance-{{ $child->id }}" class="stat-foot">
                                            View Breakdown <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>

                                {{-- Term Average --}}
                                <div class="col-12 col-sm-6 col-lg-3 mb-3">
                                    <div class="stat-tile">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="stat-icon stat-icon-soft-primary mr-3">
                                                <i class="fas fa-graduation-cap"></i>
                                            </div>
                                            <div>
                                                <div class="stat-value">
                                                    {{ $data['average'] ?? 'N/A' }}{{ $data['average'] ? '%' : '' }}
                                                </div>
                                                <div class="stat-label">Term Average</div>
                                            </div>
                                        </div>
                                        <a href="#grades-{{ $child->id }}" class="stat-foot">
                                            View Grades <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>

                                {{-- Today's Attendance --}}
                                <div class="col-12 col-sm-6 col-lg-3 mb-3">
                                    @php
                                        $status = $data['todayAttendance']?->status;
                                        $tileIcon = 'stat-icon-soft-secondary';
                                        $tileValue = 'text-secondary';
                                        if ($status == 'PRESENT') { $tileIcon = 'stat-icon-soft-success'; $tileValue = 'text-success'; }
                                        if ($status == 'ABSENT')  { $tileIcon = 'stat-icon-soft-danger';  $tileValue = 'text-danger'; }
                                    @endphp
                                    <div class="stat-tile">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="stat-icon {{ $tileIcon }} mr-3">
                                                <i class="fas fa-calendar-day"></i>
                                            </div>
                                            <div>
                                                <div class="stat-value {{ $tileValue }}">
                                                    {{ $status ?? 'N/A' }}
                                                </div>
                                                <div class="stat-label">Today's Attendance</div>
                                            </div>
                                        </div>
                                        <a href="#attendance-{{ $child->id }}" class="stat-foot">
                                            View Log <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                {{-- ============ LEFT COLUMN ============ --}}
                                <div class="col-md-8">

                                    {{-- Today's Schedule --}}
                                    <div class="card card-outline card-primary shadow-sm mb-3">
                                        <div class="card-header">
                                            <h3 class="card-title font-weight-bold">
                                                <i class="fas fa-calendar-day mr-1 text-primary"></i>
                                                {{ $child->name }}'s Schedule Today
                                                <span class="badge badge-primary ml-2">{{ now()->format('l') }}</span>
                                            </h3>
                                        </div>
                                        <div class="card-body p-0">
                                            @if ($data['todayClasses']->count() > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-hover m-0">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th>Time</th>
                                                                <th>Subject</th>
                                                                <th>Teacher</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($data['todayClasses'] as $slot)
                                                                @php
                                                                    $start = Carbon\Carbon::parse($slot->start_time);
                                                                    $end = Carbon\Carbon::parse($slot->end_time);
                                                                    $isNow = now()->between($start, $end);
                                                                @endphp
                                                                <tr class="{{ $isNow ? 'table-success' : '' }}">
                                                                    <td class="align-middle">
                                                                        @if ($isNow)
                                                                            <span class="badge badge-success mr-1">
                                                                                <span class="pulse-dot"></span>NOW
                                                                            </span>
                                                                        @endif
                                                                        {{ $start->format('h:i A') }} - {{ $end->format('h:i A') }}
                                                                    </td>
                                                                    <td class="align-middle font-weight-bold">
                                                                        {{ $slot->subject->name }}
                                                                    </td>
                                                                    <td class="align-middle">{{ $slot->teacher->name ?? 'TBA' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="text-center p-4 text-muted">
                                                    <i class="fas fa-coffee fa-2x mb-2 d-block"></i>
                                                    No classes scheduled for today.
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Academic Performance --}}
                                    <div class="card card-outline card-info shadow-sm mb-3" id="grades-{{ $child->id }}">
                                        <div class="card-header">
                                            <h3 class="card-title font-weight-bold">
                                                <i class="fas fa-chart-line mr-1 text-info"></i>
                                                Academic Performance
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            @if ($grades->count() > 0)
                                                <div class="chart-wrap-lg">
                                                    <canvas id="gradeChart-{{ $child->id }}"></canvas>
                                                </div>
                                                <div class="d-flex flex-wrap justify-content-between align-items-center mt-2">
                                                    <small class="text-muted">
                                                        Hover a bar for the full score and grade
                                                    </small>
                                                    <span class="font-weight-bold">
                                                        Term Average:
                                                        <strong class="text-primary">{{ $data['average'] }}%</strong>
                                                    </span>
                                                </div>
                                            @else
                                                <div class="text-center p-4 text-muted">
                                                    <i class="fas fa-chart-bar fa-2x mb-2 d-block"></i>
                                                    No published grades yet for this term.
                                                </div>
                                            @endif
                                        </div>
                                        @if ($grades->count() > 0)
                                            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
                                                <span class="font-weight-bold text-muted">
                                                    <i class="fas fa-award mr-1 text-warning"></i>
                                                    {{ $grades->count() }} subject{{ $grades->count() > 1 ? 's' : '' }} assessed
                                                </span>
                                                <a href="{{ resolveRoute('reports.single', $child->id) }}"
                                                   class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-file-pdf"></i> Download Report Card
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Attendance Breakdown --}}
                                    <div class="card card-outline card-success shadow-sm" id="attendance-{{ $child->id }}">
                                        <div class="card-header">
                                            <h3 class="card-title font-weight-bold">
                                                <i class="fas fa-user-check mr-1 text-success"></i>
                                                Attendance Breakdown
                                            </h3>
                                        </div>
                                        <div class="card-body">

                                            {{-- Term Summary + Doughnut --}}
                                            <div class="row align-items-center mb-4">
                                                <div class="col-12 col-sm-5 col-md-4">
                                                    <div class="chart-wrap att-glance-chart">
                                                        <canvas id="attChart-{{ $child->id }}"></canvas>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-7 col-md-8">
                                                    <h6 class="font-weight-bold text-muted mb-3">
                                                        This Term at a Glance
                                                    </h6>
                                                    <div class="row text-center">
                                                        <div class="col-4">
                                                            <div class="h4 font-weight-bold text-success mb-0">{{ $data['termPresent'] }}</div>
                                                            <small class="text-muted">Present</small>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="h4 font-weight-bold text-danger mb-0">{{ $data['termAbsent'] }}</div>
                                                            <small class="text-muted">Absent</small>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="h4 font-weight-bold text-warning mb-0">
                                                                {{ max(0, $data['termTotal'] - $data['termPresent'] - $data['termAbsent']) }}
                                                            </div>
                                                            <small class="text-muted">Late</small>
                                                        </div>
                                                    </div>
                                                    <div class="text-center mt-3">
                                                        <span class="badge badge-{{ $data['termRate'] >= 75 ? 'success' : 'danger' }} px-3 py-2" style="border-radius: 20px;">
                                                            {{ $data['termRate'] }}% Attendance Rate
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Monthly Chart --}}
                                            <h6 class="font-weight-bold text-muted mb-3">
                                                Monthly Breakdown (Last 6 Months)
                                            </h6>
                                            <div class="chart-wrap">
                                                <canvas id="monthChart-{{ $child->id }}"></canvas>
                                            </div>

                                        </div>
                                    </div>

                                </div>

                                {{-- ============ RIGHT COLUMN ============ --}}
                                <div class="col-md-4">

                                    {{-- Fees & Payments --}}
                                    <div class="card card-outline card-danger shadow-sm mb-3" id="fees-{{ $child->id }}">
                                        <div class="card-header">
                                            <h3 class="card-title font-weight-bold">
                                                <i class="fas fa-file-invoice-dollar mr-1 text-danger"></i>
                                                Fees & Payments
                                            </h3>
                                        </div>
                                        <div class="card-body p-0">
                                            <ul class="list-group list-group-flush">
                                                @forelse($invoices as $invoice)
                                                    @php
                                                        $paid = $invoice->payments_sum_amount ?? 0;
                                                        $balance = $invoice->total_amount - $paid;
                                                        $pct = $invoice->total_amount > 0 ? round(($paid / $invoice->total_amount) * 100) : 100;
                                                    @endphp
                                                    <li class="list-group-item">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <div>
                                                                <div class="font-weight-bold text-sm">
                                                                    {{ $invoice->invoice_number }}
                                                                </div>
                                                                <small class="text-muted">
                                                                    Due:
                                                                    {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}
                                                                </small>
                                                            </div>
                                                            <span class="badge badge-{{ $invoice->status == 'PAID' ? 'success' : ($invoice->status == 'PARTIAL' ? 'warning' : 'danger') }}">
                                                                {{ $invoice->status }}
                                                            </span>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <small class="text-muted">
                                                                ₦{{ number_format($paid) }} of ₦{{ number_format($invoice->total_amount) }}
                                                            </small>
                                                            @if ($balance > 0)
                                                                <small class="text-danger font-weight-bold">
                                                                    ₦{{ number_format($balance) }} due
                                                                </small>
                                                            @else
                                                                <small class="text-success font-weight-bold">
                                                                    <i class="fas fa-check-circle mr-1"></i>Paid in Full
                                                                </small>
                                                            @endif
                                                        </div>
                                                        <div class="progress fee-progress mb-2">
                                                            <div class="progress-bar {{ $pct >= 100 ? 'bg-success' : 'bg-warning' }}"
                                                                 style="width: {{ $pct }}%;"></div>
                                                        </div>
                                                        @if ($balance > 0)
                                                            <button class="btn btn-xs btn-danger float-right"
                                                                    data-toggle="modal" data-target="#payModal-{{ $invoice->id }}">
                                                                Pay Now
                                                            </button>
                                                        @endif
                                                    </li>
                                                @empty
                                                    <li class="list-group-item text-center text-muted p-4">
                                                        <i class="fas fa-file-invoice-dollar fa-2x mb-2 d-block"></i>
                                                        No invoices for this term.
                                                    </li>
                                                @endforelse
                                            </ul>
                                        </div>
                                        @if ($data['outstandingBalance'] > 0)
                                            <div class="card-footer bg-gradient-danger text-white text-center">
                                                <strong>Total Outstanding:
                                                    ₦{{ number_format($data['outstandingBalance']) }}</strong>
                                            </div>
                                        @else
                                            <div class="card-footer bg-gradient-success text-white text-center">
                                                <strong><i class="fas fa-check-circle mr-1"></i> All Fees Paid</strong>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Quick Actions --}}
                                    <div class="card card-outline card-warning shadow-sm">
                                        <div class="card-header">
                                            <h3 class="card-title font-weight-bold">
                                                <i class="fas fa-bolt mr-1 text-warning"></i> Quick Actions
                                            </h3>
                                        </div>
                                        <div class="card-body pt-4 pb-4">
                                            <div class="row text-center quick-actions">
                                                <div class="col-4">
                                                    <a class="btn btn-app bg-gradient-danger mb-0" href="{{ resolveRoute('reports.single', $child->id) }}">
                                                        <i class="fas fa-file-pdf"></i>Report
                                                    </a>
                                                </div>
                                                <div class="col-4">
                                                    <a class="btn btn-app bg-gradient-success mb-0" href="{{ resolveRoute('messages.index') }}">
                                                        <i class="fas fa-comments"></i>Message
                                                    </a>
                                                </div>
                                                <div class="col-4">
                                                    <a class="btn btn-app bg-gradient-info mb-0" href="{{ resolveRoute('announcements.index') }}">
                                                        <i class="fas fa-bullhorn"></i>News
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            @if (!$loop->last)
                                <hr class="my-5" style="border-top: 3px dashed #dee2e6;">
                            @endif
                        </div>
                    @endforeach

                </div>
            </div>
        </section>
    </div>

    {{-- ============ PAY MODALS ============ --}}
    @foreach ($childrenData as $data)
        @foreach ($data['invoices'] as $invoice)
            @php $balance = $invoice->total_amount - ($invoice->payments_sum_amount ?? 0); @endphp
            @if ($balance > 0)
                <div class="modal fade" id="payModal-{{ $invoice->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ resolveRoute('payments.store', $invoice->id) }}" method="POST">
                                @csrf
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">
                                        Pay Invoice: {{ $invoice->invoice_number }}
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-info">
                                        Outstanding Balance:
                                        <strong>₦{{ number_format($balance) }}</strong>
                                    </div>
                                    <div class="form-group">
                                        <label>Amount Paying</label>
                                        <input type="number" name="amount" class="form-control"
                                               max="{{ $balance }}" step="0.01" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Payment Method</label>
                                        <select name="method" class="form-control" required>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                            <option value="Cash">Cash</option>
                                            <option value="POS / Card">POS / Card</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Reference / Teller No.</label>
                                        <input type="text" name="reference" class="form-control"
                                               placeholder="e.g. Bank teller number">
                                    </div>
                                    <div class="form-group">
                                        <label>Payment Date</label>
                                        <input type="date" name="payment_date" class="form-control"
                                               value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-check"></i> Confirm Payment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endforeach
@endsection

@push('scripts')
<script>
    (function () {
        var childrenData = @json($chartData);
        var charts = {};

        Chart.defaults.global.defaultFontFamily = "'Nunito', sans-serif";
        Chart.defaults.global.defaultFontColor = '#64748b';

        function letterFor(score) {
            if (score >= 70) return 'A';
            if (score >= 60) return 'B';
            if (score >= 50) return 'C';
            if (score >= 40) return 'D';
            return 'F';
        }

        function scoreColor(score) {
            if (score >= 70) return '#10b981';
            if (score >= 50) return '#f59e0b';
            return '#ef4444';
        }

        function initChild(id) {
            if (!childrenData[id] || charts[id]) return;
            var d = childrenData[id];
            var list = [];

            // Term attendance doughnut
            var attCtx = document.getElementById('attChart-' + id);
            if (attCtx) {
                var total = d.present + d.absent + d.late;
                var doughnut = new Chart(attCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Present', 'Absent', 'Late'],
                        datasets: [{
                            data: [d.present, d.absent, d.late],
                            backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutoutPercentage: 68,
                        legend: { display: false },
                        plugins: {
                            centerText: total > 0 ? Math.round(d.rate) + '%' : '--'
                        },
                        tooltips: {
                            callbacks: {
                                label: function (item) {
                                    return ' ' + item.label + ': ' + item.value;
                                }
                            }
                        }
                    }
                });
                list.push(doughnut);
            }

            // Monthly bar chart
            var monthCtx = document.getElementById('monthChart-' + id);
            if (monthCtx) {
                var bar = new Chart(monthCtx, {
                    type: 'bar',
                    data: {
                        labels: d.months,
                        datasets: [
                            { label: 'Present', data: d.monthlyPresent, backgroundColor: '#10b981' },
                            { label: 'Absent', data: d.monthlyAbsent, backgroundColor: '#ef4444' },
                            { label: 'Late', data: d.monthlyLate, backgroundColor: '#f59e0b' }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 10, padding: 14, fontSize: 11 }
                        },
                        scales: {
                            yAxes: [{
                                ticks: { beginAtZero: true, precision: 0, fontSize: 11 },
                                gridLines: { drawBorder: false }
                            }],
                            xAxes: [{
                                gridLines: { display: false },
                                ticks: { fontSize: 11, maxRotation: 45, autoSkip: true, maxTicksLimit: 12 }
                            }]
                        }
                    }
                });
                list.push(bar);
            }

            // Grades horizontal bar
            var gradeCtx = document.getElementById('gradeChart-' + id);
            if (gradeCtx && d.subjects.length) {
                var bars = new Chart(gradeCtx, {
                    type: 'horizontalBar',
                    data: {
                        labels: d.subjects,
                        datasets: [{
                            label: 'Score',
                            data: d.scores,
                            backgroundColor: d.scores.map(scoreColor),
                            barThickness: 18
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: { display: false },
                        scales: {
                            xAxes: [{
                                ticks: { beginAtZero: true, max: 100, fontSize: 11 },
                                gridLines: { drawBorder: false }
                            }],
                            yAxes: [{
                                gridLines: { display: false },
                                ticks: { fontSize: 11 }
                            }]
                        },
                        tooltips: {
                            callbacks: {
                                label: function (item) {
                                    var score = d.scores[item.index];
                                    return ' ' + score + '% (Grade ' + letterFor(score) + ')';
                                }
                            }
                        }
                    }
                });
                list.push(bars);
            }

            charts[id] = list;
        }

        // Doughnut center text
        Chart.pluginService.register({
            beforeDraw: function (chart) {
                if (chart.config.type !== 'doughnut' || !chart.config.options.plugins) return;
                var text = chart.config.options.plugins.centerText;
                if (!text) return;
                var ctx = chart.chart.ctx;
                ctx.save();
                var fontSize = Math.min(chart.chart.height / 7, 26);
                ctx.font = '800 ' + fontSize + 'px Nunito, sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = '#334155';
                ctx.fillText(text, chart.chart.width / 2, chart.chart.height / 2);
                ctx.restore();
            }
        });

        // Lazy init on tab shown (avoids zero-size canvases in hidden panes)
        $('#childTabs a[data-toggle="tab"]').on('shown.bs.tab', function () {
            var id = $(this).attr('href').replace('#child-', '');
            initChild(id);
            (charts[id] || []).forEach(function (c) { c.resize(); });
        });

        // Init the first (visible) tab
        initChild(@json((string) $children->first()->id));
    })();
</script>
@endpush
