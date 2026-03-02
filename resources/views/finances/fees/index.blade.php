@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0">Fee Management & Invoicing</h1>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="row">
                    <div class="col-md-7">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Setup Fees for Active Term:
                                    <strong>{{ $activeTerm->name ?? 'None' }}</strong></h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('fees.store') }}" method="POST" class="mb-4">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <select name="class_level_id" class="form-control" required>
                                                <option value="">-- Class Level --</option>
                                                @foreach ($classLevels as $level)
                                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" name="name" class="form-control"
                                                placeholder="Fee Name (e.g. Tuition)" required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="amount" class="form-control" placeholder="Amount"
                                                step="0.01" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary w-100">Add</button>
                                        </div>
                                    </div>
                                </form>

                                <table class="table table-bordered table-striped">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Class Level</th>
                                            <th>Fee Breakdown</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($feeStructures as $classId => $fees)
                                            <tr>
                                                <td class="font-weight-bold align-middle">
                                                    {{ $fees->first()->classLevel->name }}</td>
                                                <td>
                                                    <ul class="mb-0 pl-3">
                                                        @foreach ($fees as $fee)
                                                            <li>{{ $fee->name }}: ${{ number_format($fee->amount, 2) }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                                <td class="font-weight-bold text-success align-middle text-lg">
                                                    ${{ number_format($fees->sum('amount'), 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="card card-outline card-success">
                            <div class="card-header">
                                <h3 class="card-title">Batch Generate Invoices</h3>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Select a class level to automatically generate invoices for all
                                    enrolled students based on the fee structures defined on the left.</p>

                                <form action="{{ route('invoices.generate') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label>Target Class Level</label>
                                        <select name="class_level_id" class="form-control" required>
                                            <option value="">-- Select Class to Invoice --</option>
                                            @foreach ($classLevels as $level)
                                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Invoice Due Date</label>
                                        <input type="date" name="due_date" class="form-control" required>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-block btn-lg"
                                        onclick="return confirm('Generate invoices for all students in this class? This cannot be undone.')">
                                        <i class="fas fa-file-invoice-dollar"></i> Generate Student Invoices
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
