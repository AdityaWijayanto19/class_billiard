<?php

namespace App\Policies;

use App\Models\payments;
use App\Models\User;

/**
 * PaymentPolicy
 *
 * Model-level authorization untuk Payment
 * Digunakan untuk: $user->can('confirm', $payment)
 */
class PaymentPolicy
{
    /**
     * View payments
     */
    public function viewAny(User $user): bool
    {
        return $user->can('payment.view');
    }

    /**
     * View specific payment
     */
    public function view(User $user, payments $payment): bool
    {
        return $user->can('payment.view');
    }

    /**
     * Confirm payment received (admin only)
     */
    public function confirm(User $user, payments $payment): bool
    {
        if (! $user->can('payment.confirm')) {
            return false;
        }

        // Payment harus pending
        if ($payment->status !== 'pending') {
            return false;
        }

        // Admin hanya bisa confirm cash payments
        if ($user->hasRole('admin') && $payment->method !== 'cash') {
            return false;
        }

        return true;
    }

    /**
     * Refund payment (super_admin only)
     */
    public function refund(User $user, payments $payment): bool
    {
        if (! $user->can('payment.refund')) {
            return false;
        }

        // Hanya payment confirmed yang bisa di-refund
        if ($payment->status !== 'confirmed') {
            return false;
        }

        return true;
    }

    /**
     * View payment reports
     */
    public function viewReports(User $user): bool
    {
        return $user->can('payment.view_reports');
    }

    /**
     * Export payment data
     */
    public function export(User $user): bool
    {
        return $user->can('payment.export');
    }

    /**
     * View audit log
     */
    public function auditLog(User $user): bool
    {
        return $user->can('payment.audit_log');
    }
}
