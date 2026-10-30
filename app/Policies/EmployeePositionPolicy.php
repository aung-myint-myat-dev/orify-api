<?php

namespace App\Policies;

use App\Models\EmployeePosition;
use App\Models\User;

class EmployeePositionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, EmployeePosition $employeePosition): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, EmployeePosition $employeePosition): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, EmployeePosition $employeePosition): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, EmployeePosition $employeePosition): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, EmployeePosition $employeePosition): bool
    {
        return $user->isAdmin();
    }
}
