<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Determine whether the user can view the payment.
     * Same ownership chain via $payment->invoice->student.
     */
    public function view(User $user, Payment $payment): bool
    {
        $invoice = $payment->invoice;

        if (! $invoice) {
            return false;
        }

        // Tenant boundary.
        if (session()->has('active_school') && session('active_school')) {
            if ((int) $payment->school_id !== (int) session('active_school')) {
                return false;
            }
            if ((int) $invoice->school_id !== (int) session('active_school')) {
                return false;
            }
        }

        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        if ($user->hasRole('SchoolAdmin') || $user->hasRole('Bursar')) {
            return (int) $user->school_id === (int) $invoice->school_id;
        }

        if ((int) $user->id === (int) $invoice->student_id) {
            return true;
        }

        if ($user->hasRole('Parent')) {
            return $user->children()->where('users.id', $invoice->student_id)->exists()
                || $user->children()->where('student_id', $invoice->student_id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can view/download the receipt PDF.
     * Same as view.
     */
    public function viewReceipt(User $user, Payment $payment): bool
    {
        return $this->view($user, $payment);
    }
}
