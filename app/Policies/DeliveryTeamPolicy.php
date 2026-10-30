<?php

namespace App\Policies;

use App\Models\DeliveryTeam;
use App\Models\User;

class DeliveryTeamPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DeliveryTeam $deliveryTeam): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (! $user->isDriver() && ! $user->isDeliveryStaff()) {
            return false;
        }

        return $deliveryTeam->deliveryTeamMembers()
            ->whereHas('employee', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereNull('left_at')
            ->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DeliveryTeam $deliveryTeam): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DeliveryTeam $deliveryTeam): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DeliveryTeam $deliveryTeam): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DeliveryTeam $deliveryTeam): bool
    {
        return $user->isAdmin();
    }
}
