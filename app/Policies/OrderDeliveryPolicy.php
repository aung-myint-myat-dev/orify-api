<?php

namespace App\Policies;

use App\Models\OrderDelivery;
use App\Models\User;

class OrderDeliveryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isDriver() || $user->isDeliveryStaff();
    }

    public function view(User $user, OrderDelivery $orderDelivery): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCustomer()) {
            return $user->customer?->id === $orderDelivery->order?->customer_id;
        }

        if (! $user->isDriver() && ! $user->isDeliveryStaff()) {
            return false;
        }

        return $orderDelivery->deliveredBy?->deliveryTeamMembers()
            ->whereHas('employee', fn ($query) => $query->where('user_id', $user->id))
            ->whereNull('left_at')
            ->exists() ?? false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, OrderDelivery $orderDelivery): bool
    {
        return $user->isAdmin()
            || (($user->isDriver() || $user->isDeliveryStaff()) && $this->view($user, $orderDelivery));
    }

    public function delete(User $user, OrderDelivery $orderDelivery): bool
    {
        return $user->isAdmin();
    }
}
