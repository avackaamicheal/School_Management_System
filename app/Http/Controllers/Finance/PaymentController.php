<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\School;
use App\Models\User;
use App\Notifications\PaymentReceivedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    // 1. List Invoices
    public function index(School $school, Request $request)
    {
        $invoices = Invoice::with(['student.studentProfile', 'payments'])
            ->when($request->search, function ($q) use ($request) {
                $q->where('invoice_number', 'like', "%{$request->search}%")
                    ->orWhereHas('student', function ($q) use ($request) {
                        $q->where('name', 'like', "%{$request->search}%");
                    });
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

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

        // Record payment
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $request->amount,
            'method' => $request->method,
            'reference' => $request->reference,
            'payment_date' => $request->payment_date,
        ]);

        // Refresh invoice and update status
        $invoice->refresh();
        $invoice->update([
            'status' => $invoice->balance() <= 0 ? 'PAID' : 'PARTIAL'
        ]);

        // Notify parents and admin — wrapped separately
        try {
            $payment->load('invoice.student.parents');

            $payment->invoice->student->parents->each(function ($parent) use ($payment) {
                $parent->notify(new PaymentReceivedNotification($payment));
            });

            $schoolAdmin = User::where('school_id', session('active_school'))
                ->role('SchoolAdmin')
                ->first();

            if ($schoolAdmin) {
                $schoolAdmin->notify(new PaymentReceivedNotification($payment));
            }
        } catch (\Exception $e) {
            Log::warning("Payment notification failed for payment {$payment->id}: " . $e->getMessage());
        }

        return back()->with('success', 'Payment recorded successfully!');
    }
    // 3. Generate PDF Receipt
    public function receipt(School $school, Payment $payment)
    {
        // Load relationships needed for the receipt
        $payment->load(['invoice.student.studentProfile', 'invoice.items']);

        $pdf = Pdf::loadView('finances.payments.receipt', compact('payment', 'school'));

        return $pdf->download('Receipt_' . $payment->invoice->invoice_number . '.pdf');
    }
}
