<?php

namespace App\Policies;

use App\Models\orders;
use App\Models\User;

/**
 * OrderPolicy
 *
 * Model-level authorization untuk Order
 * Digunakan untuk: $user->can('view', $order)
 */
class OrderPolicy
{
    /**
     * View list of orders
     */
    public function viewAny(User $user): bool
    {
        return $user->can('order.view');
    }

    /**
     * View specific order
     */
    public function view(User $user, orders $order): bool
    {
        return $user->can('order.show');
    }

    /**
     * Create new order
     */
    public function create(User $user): bool
    {
        return $user->can('order.create');
    }

    /**
     * Update order
     */
    public function update(User $user, orders $order): bool
    {
        // User harus punya permission
        if (! $user->can('order.update')) {
            return false;
        }

        // Order harus unpaid atau pending
        if (! in_array($order->status, ['pending', 'pending_unpaid'])) {
            return false;
        }

        return true;
    }

    /**
     * Delete order - HANYA untuk pending orders (audit safety)
     */
    public function delete(User $user, orders $order): bool
    {
        // Only super_admin can delete
        if (! $user->can('order.delete')) {
            return false;
        }

        // Only pending orders can be deleted
        if (! $order->canBeDeleted()) {
            return false;
        }

        return true;
    }

    /**
     * Mark order as ready (kitchen)
     */
    public function markReady(User $user, orders $order): bool
    {
        if (! $user->can('order.mark_ready')) {
            return false;
        }

        // Order harus confirmed (sudah paid)
        if ($order->status !== 'confirmed') {
            return false;
        }

        return true;
    }

    /**
     * Complete order (kitchen)
     */
    public function complete(User $user, orders $order): bool
    {
        if (! $user->can('order.complete')) {
            return false;
        }

        // Order harus ready atau processing
        if (! in_array($order->status, ['ready', 'processing'])) {
            return false;
        }

        return true;
    }

    /**
     * Cancel order
     */
    public function cancel(User $user, orders $order): bool
    {
        if (! $user->can('order.cancel')) {
            return false;
        }

        // Cannot cancel completed, rejected, atau expired orders
        if (in_array($order->status, ['completed', 'rejected', 'expired_unpaid'])) {
            return false;
        }

        return true;
    }

    /**
     * View order history
     */
    public function viewHistory(User $user): bool
    {
        return $user->can('order.view_history');
    }
}
