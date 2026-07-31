@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Bursar Dashboard</h1>
                </div>
                <div class="col-sm-6 text-left text-md-right mt-2">
                    @if ($activeTerm)
                        <span class="badge badge-success px-3 py-2 text-sm elevation-1" style="border-radius: 20px;">
                            <i class="fas fa-calendar-alt mr-1"></i> {{ $activeTerm->name }}
                        </span>
                    @else
                        <span class="badge badge-danger px-3 py-2 text-sm elevation-1" style="border-radius: 20px;">
                            <i class="fas fa-exclamation-triangle mr-1"></i> No Active Term
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            @if (!$activeTerm)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    No active term is set. Contact the school admin to activate an academic term.
                </div>
            @endif

            <div class="row">
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ Number::currency($totalExpected, 'NGN') }}</h3>
                            <p>Total Expected</p>
                        </div>
                        <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
                        <a href="{{ route('bursar.invoices.index') }}" class="small-box-footer">
                            View Invoices <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ Number::currency($totalCollected, 'NGN') }}</h3>
                            <p>Total Collected</p>
                        </div>
                        <div class="icon"><i class="fas fa-hand-holding-usd"></i></div>
                        <a href="{{ route('bursar.invoices.index') }}" class="small-box-footer">
                            View Payments <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ Number::currency($totalOutstanding, 'NGN') }}</h3>
                            <p>Total Outstanding</p>
                        </div>
                        <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
                        <a href="{{ route('bursar.reports.index') }}" class="small-box-footer">
                            View Reports <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-7">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-file-invoice mr-1"></i> Recent Invoices</h3>
                            <div class="card-tools">
                                <a href="{{ route('bursar.invoices.index') }}" class="btn btn-sm btn-outline-primary">
                                    All Invoices
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Invoice #</th>
                                            <th>Student</th>
                                            <th>Amount</th>
                                            <th>Paid</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentInvoices as $invoice)
                                            <tr>
                                                <td class="text-muted">{{ $invoice->invoice_number }}</td>
                                                <td>{{ $invoice->student->name ?? 'N/A' }}</td>
                                                <td>{{ Number::currency($invoice->total_amount, 'NGN') }}</td>
                                                <td>{{ Number::currency($invoice->payments_sum_amount ?? 0, 'NGN') }}</td>
                                                <td>
                                                    @php $status = strtolower($invoice->status); @endphp
                                                    <span class="badge badge-{{ $status === 'paid' ? 'success' : ($status === 'partial' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($invoice->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">
                                                    No invoices for the active term.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-money-bill-wave mr-1"></i> Recent Payments</h3>
                            <div class="card-tools">
                                <a href="{{ route('bursar.invoices.index') }}" class="btn btn-sm btn-outline-success">
                                    All Payments
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse($recentPayments as $payment)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="font-weight-bold small">
                                                {{ $payment->invoice->student->name ?? 'N/A' }}
                                            </div>
                                            <small class="text-muted">
                                                {{ $payment->invoice->invoice_number ?? 'N/A' }} &middot;
                                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                                            </small>
                                        </div>
                                        <span class="text-success font-weight-bold">
                                            +{{ Number::currency($payment->amount, 'NGN') }}
                                        </span>
                                    </li>
                                @empty
                                    <li class="list-group-item text-center py-4 text-muted">
                                        No payments recorded yet.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
