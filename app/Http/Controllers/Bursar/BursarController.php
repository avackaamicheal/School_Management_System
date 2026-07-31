<?php

namespace App\Http\Controllers\Bursar;

use App\Http\Controllers\Controller;
use App\Models\BursarProfile;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\School;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BursarController extends Controller
{
    public function index(School $school)
    {
        $activeTerm = Term::where('is_active', true)->first();

        $invoices = collect();
        if ($activeTerm) {
            $invoices = Invoice::with(['student.studentProfile', 'student.school'])
                ->where('term_id', $activeTerm->id)
                ->withSum('payments', 'amount')
                ->orderBy('status', 'asc')
                ->get();
        }

        $totalExpected = $invoices->sum('total_amount');
        $totalCollected = $invoices->sum('payments_sum_amount');
        $totalOutstanding = $totalExpected - $totalCollected;

        $recentInvoices = $invoices->take(8);
        $recentPayments = Payment::with('invoice.student')
            ->latest()
            ->take(8)
            ->get();

        return view('bursar.index', compact(
            'invoices',
            'activeTerm',
            'totalExpected',
            'totalCollected',
            'totalOutstanding',
            'recentInvoices',
            'recentPayments'
        ));
    }

    public function manage(Request $request, School $school)
    {
        $bursars = User::role('Bursar')
            ->where('school_id', session('active_school'))
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhereHas('bursarProfile', function ($q) use ($request) {
                        $q->where('employee_id', 'like', "%{$request->search}%")
                            ->orWhere('phone', 'like', "%{$request->search}%");
                    });
            })
            ->with('bursarProfile')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('bursar.manage', compact('bursars'));
    }

    public function create(School $school)
    {
        return view('bursar.create');
    }

    public function store(Request $request, School $school)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:8|confirmed',
            'phone'      => 'required|string|max:20',
            'address'    => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name' => trim($request->first_name . ' ' . $request->last_name),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'school_id' => session('active_school'),
        ]);

        $user->assignRole('Bursar');

        BursarProfile::create([
            'user_id' => $user->id,
            'school_id' => session('active_school'),
            'employee_id' => BursarProfile::generateEmployeeId(),
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('bursars.index')
            ->with('success', 'Bursar added successfully! Employee ID: ' . $user->bursarProfile->employee_id);
    }

    public function edit(School $school, User $bursar)
    {
        $bursar->load('bursarProfile');
        return view('bursar.edit', compact('bursar'));
    }

    public function update(Request $request, School $school, User $bursar)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $bursar->id,
            'password'  => 'nullable|min:8|confirmed',
            'phone'     => 'required|string|max:20',
            'address'   => 'nullable|string|max:500',
        ]);

        $bursar->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $bursar->update(['password' => Hash::make($request->password)]);
        }

        $bursar->bursarProfile()->updateOrCreate(
            ['user_id' => $bursar->id],
            [
                'employee_id' => $bursar->bursarProfile->employee_id ?? BursarProfile::generateEmployeeId(),
                'phone' => $request->phone,
                'address' => $request->address,
            ]
        );

        return redirect()->route('bursars.index')
            ->with('success', 'Bursar updated successfully!');
    }

    public function destroy(School $school, User $bursar)
    {
        if ($bursar->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $bursar->bursarProfile()->delete();
        $bursar->delete();

        return back()->with('success', 'Bursar removed successfully!');
    }
}
