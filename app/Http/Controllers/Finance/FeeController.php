<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\ClassLevel;
use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\School;
use App\Models\Term;
use App\Models\User;
use App\Notifications\InvoiceGeneratedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FeeController extends Controller
{
    public function index(School $school)
    {
        $classLevels = ClassLevel::all();
        $activeTerm = Term::getActive();

        $feeStructures = FeeStructure::with('classLevel')
            ->where('term_id', $activeTerm?->id)
            ->get()
            ->groupBy('class_level_id');

        return view('finances.fees.index', compact('classLevels', 'activeTerm', 'feeStructures'));
    }

    public function store(Request $request, School $school)
    {
        $request->validate([
            'class_level_id' => 'required|exists:class_levels,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $activeTerm = Term::getActive();

        FeeStructure::create([
            'term_id' => $activeTerm->id,
            'class_level_id' => $request->class_level_id,
            'name' => $request->name,
            'amount' => $request->amount,
        ]);

        return back()->with('success', 'Fee structure added successfully!');
    }

    public function generateInvoices(Request $request, School $school)
    {
        $request->validate([
            'class_level_id' => 'required|exists:class_levels,id',
            'due_date' => 'required|date',
        ]);

        $activeTerm = Term::getActive();
        $classLevelId = $request->class_level_id;

        // 1. Get the configured fees for this class
        $fees = FeeStructure::where('term_id', $activeTerm->id)
            ->where('class_level_id', $classLevelId)
            ->get();

        if ($fees->isEmpty()) {
            return back()->with('error', 'No fees configured for this class level.');
        }

        // 2. Get all students in this class level
        $students = User::role('Student')->whereHas('studentProfile.section', function ($query) use ($classLevelId) {
            $query->where('class_level_id', $classLevelId);
        })->get();

        $generatedCount = 0;

        // 3. Loop through students and build their invoices
        foreach ($students as $student) {

            // Prevent duplicate invoices for the same term
            $existingInvoice = Invoice::where('term_id', $activeTerm->id)
                ->where('student_id', $student->id)
                ->first();

            if (!$existingInvoice) {
                // Create the master invoice
                $invoice = Invoice::create([
                    'term_id' => $activeTerm->id,
                    'student_id' => $student->id,
                    'invoice_number' => 'INV-' . strtoupper(Str::random(6)) . '-' . date('Y'),
                    'total_amount' => $fees->sum('amount'),
                    'due_date' => $request->due_date,
                    'status' => 'UNPAID',
                ]);

                foreach ($student->parents as $parent) {
                    $parent->notify(new InvoiceGeneratedNotification($invoice));
                }

                // Create the individual line items
                foreach ($fees as $fee) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'name' => $fee->name,
                        'amount' => $fee->amount,
                    ]);
                }

                // Notify parents — wrapped separately
                try {
                    foreach ($student->parents as $parent) {
                        $parent->notify(new InvoiceGeneratedNotification($invoice));
                        
                    }
                } catch (\Exception $e) {
                    Log::warning("Invoice notification failed for student {$student->id}: " . $e->getMessage());
                }

                $generatedCount++;
            }
        }

        return back()->with('success', "Successfully generated {$generatedCount} invoices for this class level!");
    }
}
