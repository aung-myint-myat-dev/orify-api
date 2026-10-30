<?php

namespace App\Policies;

use App\Enums\AddressRegistarationStatus;
use App\Models\AddressRegistration;
use App\Models\User;

class AddressRegistrationPolicy
{
    /**
     * Determine whether the user can view any registrations.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_active
            && ($user->isAdmin() || $user->isCustomer());
    }

    /**
     * Determine whether the user can view a registration.
     */
    public function view(
        User $user,
        AddressRegistration $addressRegistration
    ): bool {
        if (! $user->is_active) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $user->isCustomer()
            && $addressRegistration->address?->customer?->user_id === $user->id;
    }

    /**
     * Determine whether the user can create a registration.
     */
    public function create(User $user): bool
    {
        return $user->is_active
            && $user->isCustomer();
    }

    /**
     * Determine whether the user can update a registration.
     */
    public function update(
        User $user,
        AddressRegistration $addressRegistration
    ): bool {
        return $user->is_active
            && $user->isCustomer()
            && $addressRegistration->address?->customer?->user_id === $user->id
            && $addressRegistration->status === AddressRegistarationStatus::PENDING;
    }

    /**
     * Determine whether the user can cancel a registration.
     */
    public function cancel(
        User $user,
        AddressRegistration $addressRegistration
    ): bool {
        return $user->is_active
            && $user->isCustomer()
            && $addressRegistration->address?->customer?->user_id === $user->id
            && $addressRegistration->status === AddressRegistarationStatus::PENDING;
    }

    /**
     * Determine whether the user can approve a registration.
     */
    public function approve(
        User $user,
        AddressRegistration $addressRegistration
    ): bool {
        return $user->is_active
            && $user->isAdmin()
            && $addressRegistration->status === AddressRegistarationStatus::PENDING;
    }

    /**
     * Determine whether the user can reject a registration.
     */
    public function reject(
        User $user,
        AddressRegistration $addressRegistration
    ): bool {
        return $user->is_active
            && $user->isAdmin()
            && $addressRegistration->status === AddressRegistarationStatus::PENDING;
    }

    /**
     * Determine whether the user can delete a registration.
     */
    public function delete(
        User $user,
        AddressRegistration $addressRegistration
    ): bool {
        return false;
    }

    /**
     * Determine whether the user can restore a registration.
     */
    public function restore(
        User $user,
        AddressRegistration $addressRegistration
    ): bool {
        return false;
    }

    /**
     * Determine whether the user can permanently delete a registration.
     */
    public function forceDelete(
        User $user,
        AddressRegistration $addressRegistration
    ): bool {
        return false;
    }
}
