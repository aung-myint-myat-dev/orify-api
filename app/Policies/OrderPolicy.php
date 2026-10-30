<?php

namespace App\Policies;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Check whether the user owns the order.
     */
    private function isOwner(User $user, Order $order): bool
    {
        return $user->customer?->id === $order->customer_id
            && $user->customer !== null;
    }

    /**
     * Check whether the order is pending.
     */
    private function isPending(Order $order): bool
    {
        return $order->order_status === OrderStatus::PENDING;
    }

    /**
     * Determine whether the user can view any orders.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view an order.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->isAdmin()
            || $this->isOwner($user, $order);
    }

    /**
     * Determine whether the user can create an order.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin()
            || $user->isCustomer();
    }

    /**
     * Determine whether the user can update an order.
     */
    public function update(User $user, Order $order): bool
    {
        return $user->isAdmin()
            || ($this->isOwner($user, $order) && $this->isPending($order));
    }

    /**
     * Determine whether the user can delete an order.
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin()
            && $this->isPending($order);
    }

    /**
     * Determine whether the user can restore an order.
     */
    public function restore(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete an order.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return false;
    }

    /**
     * Determine whether the user can cancel an order.
     */
    public function cancel(User $user, Order $order): bool
    {
        return $this->isPending($order)
            && ($user->isAdmin() || $this->isOwner($user, $order));
    }

    /**
     * Determine whether customer can view own order.
     */
    public function viewOwn(User $user): bool
    {
        return $user->isCustomer() && $user->customer !== null;
    }
}
