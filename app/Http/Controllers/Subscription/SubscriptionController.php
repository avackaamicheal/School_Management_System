<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Models\SchoolSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Notifications\NewSchoolRegistrationNotification;
use App\Notifications\SubscriptionActivatedNotification;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function __construct(protected PaystackService $paystack)
    {
    }

    public function index()
    {
        $school = Auth::user()->school;
        $plans = SubscriptionPlan::where('is_active', true)->get();

        $currentSubscription = $school->activeSubscription()->with('plan')->first();
        $studentCount = User::role('Student')
            ->where('school_id', $school->id)
            ->count();

        return view('subscription.index', compact(
            'school',
            'plans',
            'currentSubscription',
            'studentCount'
        ));
    }

    public function initiate(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $school = Auth::user()->school;
        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        $reference = 'SUB-' . strtoupper(Str::random(10)) . '-' . time();

        // Store in session for verification
        session([
            'subscription_reference' => $reference,
            'subscription_plan_id' => $plan->id,
        ]);

        $response = $this->paystack->initializeTransaction([
            'email' => Auth::user()->email,
            'amount' => $plan->price * 100, // kobo
            'reference' => $reference,
            'currency' => 'NGN',
            'callback_url' => route('subscription.callback'),
            'metadata' => [
                'school_id' => $school->id,
                'plan_id' => $plan->id,
                'school_name' => $school->name,
            ],
        ]);

        if ($response['status']) {
            return redirect($response['data']['authorization_url']);
        }

        return back()->with('error', 'Could not initiate payment. Please try again.');
    }

    public function callback(Request $request)
    {
        $reference = $request->reference ?? session('subscription_reference');

        if (!$reference) {
            return redirect()->route('subscription.index')
                ->with('error', 'Invalid payment reference.');
        }

        $paymentDetails = $this->paystack->verifyTransaction($reference);

        if (!$paymentDetails['status'] || $paymentDetails['data']['status'] !== 'success') {
            return redirect()->route('subscription.index')
                ->with('error', 'Payment verification failed. Please contact support.');
        }

        $data = $paymentDetails['data'];
        $school = Auth::user()->school;
        $planId = $data['metadata']['plan_id'] ?? session('subscription_plan_id');
        $plan = SubscriptionPlan::findOrFail($planId);

        // Calculate subscription period
        $existingSub = $school->activeSubscription()->first();
        $startsAt = $existingSub && $existingSub->expires_at->isFuture()
            ? $existingSub->expires_at
            : now();
        $expiresAt = $startsAt->copy()->addDays($plan->duration_days);

        // Deactivate old subscription
        SchoolSubscription::where('school_id', $school->id)
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        // Create new subscription
        $subscription = SchoolSubscription::create([
            'school_id' => $school->id,
            'plan_id' => $plan->id,
            'amount_paid' => $data['amount'] / 100,
            'paystack_reference' => $reference,
            'status' => 'active',
            'starts_at' => $startsAt,
            'expires_at' => $expiresAt,
        ]);

        // Auto-activate the school
        $school->update([
            'is_active' => true,
            'approval_status' => 'approved',
            'approved_at' => now(),
            'is_subscribed' => true,
            'subscription_expires_at' => $expiresAt,
        ]);

        // Notify SuperAdmins of new school activation
        $superAdmins = User::role('SuperAdmin')->get();
        Notification::send($superAdmins, new NewSchoolRegistrationNotification($school));

        // Notify admin
        Auth::user()->notify(new SubscriptionActivatedNotification($subscription));

        // Clear session
        session()->forget(['subscription_reference', 'subscription_plan_id']);

        return redirect()->route('schooladmin.dashboard', ['school' => $school->slug])
            ->with('success', "Welcome! Your school is now active. Subscription valid until {$expiresAt->format('M d, Y')}.");
    }
}
