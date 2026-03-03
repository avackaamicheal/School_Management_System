@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0">Student Invoices & Payments</h1>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <div class="card card-outline card-primary">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Student</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $invoice)
                                    <tr>
                                        <td class="align-middle font-weight-bold">{{ $invoice->invoice_number }}</td>
                                        <td class="align-middle">{{ $invoice->student->name }}</td>
                                        <td class="align-middle">${{ number_format($invoice->total_amount, 2) }}</td>
                                        <td class="align-middle text-success">
                                            ${{ number_format($invoice->amountPaid(), 2) }}</td>
                                        <td class="align-middle text-danger">${{ number_format($invoice->balance(), 2) }}
                                        </td>
                                        <td class="align-middle">
                                            @if ($invoice->status == 'PAID')
                                                <span class="badge badge-success">PAID</span>
                                            @elseif($invoice->status == 'PARTIAL')
                                                <span class="badge badge-warning">PARTIAL</span>
                                            @else
                                                <span class="badge badge-danger">UNPAID</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            @if ($invoice->balance() > 0)
                                                <button class="btn btn-sm btn-primary" data-toggle="modal"
                                                    data-target="#payModal{{ $invoice->id }}">
                                                    <i class="fas fa-money-bill-wave"></i> Pay
                                                </button>
                                            @endif

                                            @if ($invoice->payments->count() > 0)
                                                <div class="btn-group">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                        data-toggle="dropdown">
                                                        <i class="fas fa-receipt"></i> Receipts
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        @foreach ($invoice->payments as $payment)
                                                            <a class="dropdown-item"
                                                                href="{{ route('payments.receipt', $payment->id) }}">
                                                                ${{ $payment->amount }} on
                                                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d') }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="payModal{{ $invoice->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('payments.store', $invoice->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title">Record Payment:
                                                            {{ $invoice->invoice_number }}</h5>
                                                        <button type="button" class="close text-white"
                                                            data-dismiss="modal"><span>&times;</span></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-info">Remaining Balance:
                                                            <strong>${{ number_format($invoice->balance(), 2) }}</strong>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Amount Paying</label>
                                                            <input type="number" name="amount" class="form-control"
                                                                max="{{ $invoice->balance() }}" step="0.01" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Payment Method</label>
                                                            <select name="method" class="form-control" required>
                                                                <option value="Cash">Cash</option>
                                                                <option value="Bank Transfer">Bank Transfer</option>
                                                                <option value="POS / Card">POS / Card</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Reference (Optional)</label>
                                                            <input type="text" name="reference" class="form-control"
                                                                placeholder="Teller No. / Trx ID">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Payment Date</label>
                                                            <input type="date" name="payment_date" class="form-control"
                                                                value="{{ date('Y-m-d') }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-success">Confirm
                                                            Payment</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
