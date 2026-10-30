<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_active && $user->isAdmin();
    }

    /**
     * Determine whether the user can view a user.
     */
    public function view(User $user, User $model): bool
    {
        return $user->is_active
            && ($user->isAdmin() || $user->id === $model->id);
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(User $user): bool
    {
        return $user->is_active && $user->isAdmin();
    }

    /**
     * Determine whether the user can update a user.
     */
    public function update(User $user, User $model): bool
    {
        return $user->is_active
            && ($user->isAdmin() || $user->id === $model->id);
    }

    /**
     * Determine whether the user can delete a user.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->is_active
            && $user->isAdmin()
            && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can restore a user.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->is_active && $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete a user.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can change another user's role.
     */
    public function changeRole(User $user, User $model): bool
    {
        return $user->is_active
            && $user->isAdmin()
            && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can change another user's active status.
     */
    public function changeStatus(User $user, User $model): bool
    {
        return $user->is_active
            && $user->isAdmin()
            && $user->id !== $model->id;
    }
}
