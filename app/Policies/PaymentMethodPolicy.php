<?php

namespace App\Policies;

use App\Models\PaymentMethod;
use App\Models\User;

class PaymentMethodPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, PaymentMethod $paymentMethod): bool
    {
        return $user->is_active
            && ($paymentMethod->is_active || $user->isAdmin());
    }

    public function create(User $user): bool
    {
        return $user->is_active && $user->isAdmin();
    }

    public function update(User $user, PaymentMethod $paymentMethod): bool
    {
        return $user->is_active && $user->isAdmin();
    }

    public function delete(User $user, PaymentMethod $paymentMethod): bool
    {
        return $user->is_active && $user->isAdmin();
    }

    public function restore(User $user, PaymentMethod $paymentMethod): bool
    {
        return $user->is_active && $user->isAdmin();
    }

    public function forceDelete(User $user, PaymentMethod $paymentMethod): bool
    {
        return false;
    }
}
