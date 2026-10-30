<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;

class AddressPolicy
{
    /**
     * Determine whether the user owns the address.
     */
    private function isOwner(User $user, Address $address): bool
    {
        return $user->customer !== null && 
            $user->customer->id === $address->customer_id;
    }

    /**
     * Determine whether the user can view any addresses.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view an address.
     */
    public function view(User $user, Address $address): bool
    {
        return $user->isAdmin() || $this->isOwner($user, $address);
    }

    /**
     * Determine whether the user can create an address.
     */
    public function create(User $user): bool
    {
        return $user->isCustomer();
    }

    /**
     * Determine whether the user can update an address.
     */
    public function update(User $user, Address $address): bool
    {
        return $user->isAdmin() || ( $user->isCustomer() && $this->isOwner($user, $address) );
    }

    /**
     * Determine whether the user can delete an address.
     */
    public function delete(User $user, Address $address): bool
    {
        return $user->isAdmin() || ( $user->isCustomer() && $this->isOwner($user, $address) );
    }

    /**
     * Determine whether the user can restore an address.
     */
    public function restore(User $user, Address $address): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete an address.
     */
    public function forceDelete(User $user, Address $address): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the address verification status.
     */
    public function updateAddressStatus(User $user, Address $address): bool
    {
        return $user->isAdmin();
    }
}
