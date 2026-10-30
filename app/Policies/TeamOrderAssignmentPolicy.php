<?php

namespace App\Policies;

use App\Models\TeamOrderAssignment;
use App\Models\User;

class TeamOrderAssignmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isDriver() || $user->isDeliveryStaff();
    }

    public function view(User $user, TeamOrderAssignment $assignment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (! $user->isDriver() && ! $user->isDeliveryStaff()) {
            return false;
        }

        return $assignment->deliveryTeam?->deliveryTeamMembers()
            ->whereHas('employee', fn ($query) => $query->where('user_id', $user->id))
            ->whereNull('left_at')
            ->exists() ?? false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, TeamOrderAssignment $assignment): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, TeamOrderAssignment $assignment): bool
    {
        return $user->isAdmin();
    }
}
