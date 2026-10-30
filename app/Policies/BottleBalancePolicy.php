<?php

namespace App\Policies;

use App\Models\BottleBalance;
use App\Models\User;

class BottleBalancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, BottleBalance $bottleBalance): bool
    {
        return $user->isAdmin()
            || $user->customer?->id === $bottleBalance->address?->customer_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, BottleBalance $bottleBalance): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, BottleBalance $bottleBalance): bool
    {
        return $user->isAdmin();
    }
}
