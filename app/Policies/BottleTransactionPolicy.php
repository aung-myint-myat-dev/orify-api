<?php

namespace App\Policies;

use App\Models\BottleTransaction;
use App\Models\User;

class BottleTransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, BottleTransaction $bottleTransaction): bool
    {
        return $user->isAdmin()
            || $user->customer?->id === $bottleTransaction->address?->customer_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, BottleTransaction $bottleTransaction): bool
    {
        return false;
    }

    public function delete(User $user, BottleTransaction $bottleTransaction): bool
    {
        return false;
    }
}
