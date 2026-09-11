<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    /**
     * Determine whether the user can view the invoice.
     * True only if: SchoolAdmin/Bursar of the invoice's school,
     * OR Parent linked to the invoice's student,
     * OR the student themselves.
     * Invoice must belong to the active school context.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        // Tenant boundary: invoice must belong to active school when in tenant context.
        if (session()->has('active_school') && session('active_school')) {
            if ((int) $invoice->school_id !== (int) session('active_school')) {
                return false;
            }
        }

        // Staff of the invoice's school.
        if ($user->hasRole('SchoolAdmin') || $user->hasRole('Bursar')) {
            return (int) $user->school_id === (int) $invoice->school_id;
        }

        // The student themselves.
        if ((int) $user->id === (int) $invoice->student_id) {
            return true;
        }

        // Parent linked to the invoice's student via parent_student pivot.
        if ($user->hasRole('Parent')) {
            return $invoice->student->parents->contains('id', $user->id);
        }

        return false;
    }

    /**
     * Determine whether the user can pay / record payment for the invoice.
     * Same ownership chain as view. SchoolAdmin/Bursar are included because
     * PaymentController@store is currently shared by SchoolAdmin, Bursar and
     * Parent routes — staff record counter payments, parents upload proof.
     */
    public function pay(User $user, Invoice $invoice): bool
    {
        return $this->view($user, $invoice);
    }
}
