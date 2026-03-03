<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // 1. List Invoices
    public function index()
    {
        // Get all invoices with student info and payments
        $invoices = Invoice::with(['student.studentProfile', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('finances.payments.index', compact('invoices'));
    }

    // 2. Process a Payment
    public function store(Request $request, $school, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $invoice->balance(),
            'method' => 'required|string',
            'reference' => 'nullable|string',
            'payment_date' => 'required|date',
        ]);

        // Record the payment
        Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $request->input('amount'),
            'method' => $request->input('method'),
            'reference' => $request->input('reference'),
            'payment_date' => $request->input('payment_date'),
        ]);

        // Refresh the invoice to get updated balance
        $invoice->refresh();

        // Update the Invoice Status dynamically
        if ($invoice->balance() <= 0) {
            $invoice->update(['status' => 'PAID']);
        } else {
            $invoice->update(['status' => 'PARTIAL']);
        }

        return back()->with('success', 'Payment recorded successfully!');
    }

    // 3. Generate PDF Receipt
    public function receipt($school, Payment $payment)
    {
        // Load relationships needed for the receipt
        $payment->load(['invoice.student.studentProfile', 'invoice.items']);

        $pdf = Pdf::loadView('finances.payments.receipt', compact('payment', 'school'));

        return $pdf->download('Receipt_' . $payment->invoice->invoice_number . '.pdf');
    }
}
