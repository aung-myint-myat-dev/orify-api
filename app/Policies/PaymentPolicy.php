<?php

namespace App\Policies;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active && ($user->isAdmin() || $user->isCustomer());
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->is_active
            && ($user->isAdmin() || $user->customer?->id === $payment->order?->customer_id);
    }

    public function create(User $user): bool
    {
        return $user->is_active && $user->isCustomer();
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->is_active
            && $payment->status === PaymentStatus::PENDING
            && ($user->isAdmin() || $user->customer?->id === $payment->order?->customer_id);
    }

    public function verify(User $user, Payment $payment): bool
    {
        return $user->is_active
            && $user->isAdmin()
            && $payment->status === PaymentStatus::PENDING;
    }

    public function delete(User $user, Payment $payment): bool
    {
        return false;
    }
}
