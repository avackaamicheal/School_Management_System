<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $payment->invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 1rem;
            color: #333;
            margin: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .details-table,
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .details-table td {
            padding: 5px;
        }

        .items-table th {
            background-color: #2c3e50;
            color: white;
            padding: 10px;
            text-align: left;
        }

        .items-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .table-wrapper {
            overflow-x: auto;
            margin-bottom: 20px;
        }

        .total-box {
            max-width: 280px;
            width: 100%;
            border: 2px solid #2c3e50;
            padding: 15px;
            background-color: #ecf0f1;
            margin: 0 0 20px auto;
            box-sizing: border-box;
        }

        @media print {
            body {
                margin: 0;
                font-size: 12pt;
            }
            .total-box {
                float: right;
                margin: 0 0 20px auto;
            }
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>{{ $school->name ? \App\Models\School::find(session('active_school'))->name : 'Official Receipt' }}
        </h1>
        <p>Payment Receipt</p>
    </div>

    <table class="details-table">
        <tr>
            <td><strong>Student:</strong> {{ $payment->invoice->student->name }}</td>
            <td><strong>Receipt Date:</strong> {{ \Carbon\Carbon::parse($payment->payment_date)->format('F j, Y') }}
            </td>
        </tr>
        <tr>
            <td><strong>Invoice Ref:</strong> {{ $payment->invoice->invoice_number }}</td>
            <td><strong>Payment Method:</strong> {{ $payment->method }} ({{ $payment->reference ?? 'N/A' }})</td>
        </tr>
    </table>

    <div class="table-wrapper">
        <table class="items-table">
            <thead>
                <tr>
                    <th>Billed For</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payment->invoice->items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td style="text-align: right;">#{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="total-box">
        <p><strong>Total Billed:</strong> #{{ number_format($payment->invoice->total_amount, 2) }}</p>
        <p><strong>Amount Paid Now:</strong> #{{ number_format($payment->amount, 2) }}</p>
        <p><strong>Remaining Balance:</strong> #{{ number_format($payment->invoice->balance(), 2) }}</p>
    </div>

    <div style="clear: both; margin-top: 50px;">
        <p>___________________________</p>
        <p>Bursar / Authorized Signature</p>
    </div>

</body>

</html>
