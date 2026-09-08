@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid py-3">

                <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                    <div>
                        <h4 class="m-0 font-weight-bold text-dark">
                            Fees & Payments
                        </h4>
                        <p class="text-muted mb-0">
                            Invoices and payment history for {{ $activeTerm?->name ?? 'the current term' }}
                        </p>
                    </div>
                    <div class="text-muted">
                        <i class="far fa-calendar-alt mr-1"></i>{{ now()->format('D, M d, Y') }}
                    </div>
                </div>

                @include('parent.partials.child-tabs', ['prefix' => 'fee'])

                <div class="tab-content">
                    @foreach ($childrenData as $data)
                        @php $child = $data['student']; @endphp
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                             id="fee-child-{{ $child->id }}" role="tabpanel">

                            {{-- Invoices --}}
                            <div class="card card-outline card-danger shadow-sm mb-3">
                                <div class="card-header">
                                    <h3 class="card-title font-weight-bold">
                                        <i class="fas fa-file-invoice-dollar mr-1 text-danger"></i>
                                        {{ $child->name }}'s Invoices
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        @forelse($data['invoices'] as $invoice)
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

                            {{-- Payment History --}}
                            @php
                                $payments = $data['invoices']
                                    ->flatMap(fn($inv) => $inv->payments->map(fn($p) => [
                                        'invoice' => $inv->invoice_number,
                                        'amount' => $p->amount,
                                        'method' => $p->method,
                                        'reference' => $p->reference,
                                        'date' => $p->payment_date,
                                    ]))
                                    ->sortByDesc('date');
                            @endphp
                            <div class="card card-outline card-warning shadow-sm">
                                <div class="card-header">
                                    <h3 class="card-title font-weight-bold">
                                        <i class="fas fa-history mr-1 text-warning"></i>
                                        Payment History
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover m-0">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Invoice</th>
                                                    <th>Method</th>
                                                    <th>Reference</th>
                                                    <th class="text-right">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($payments as $payment)
                                                    <tr>
                                                        <td class="align-middle">
                                                            {{ \Carbon\Carbon::parse($payment['date'])->format('M d, Y') }}
                                                        </td>
                                                        <td class="align-middle font-weight-bold">{{ $payment['invoice'] }}</td>
                                                        <td class="align-middle">{{ $payment['method'] }}</td>
                                                        <td class="align-middle">{{ $payment['reference'] ?? '—' }}</td>
                                                        <td class="align-middle text-right text-success font-weight-bold">
                                                            ₦{{ number_format($payment['amount']) }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center p-4 text-muted">
                                                            No payments recorded yet.
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

                {{-- Pay Modals --}}
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

            </div>
        </section>
    </div>
@endsection
