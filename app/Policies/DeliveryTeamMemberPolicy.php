<?php

namespace App\Policies;

use App\Models\DeliveryTeamMember;
use App\Models\User;

class DeliveryTeamMemberPolicy
{
    /**
     * View member list.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->isDriver()
            || $user->isDeliveryStaff();
    }

    /**
     * View a specific membership.
     */
    public function view(User $user, DeliveryTeamMember $member): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (! $user->isDriver() && ! $user->isDeliveryStaff()) {
            return false;
        }

        $employeeId = $user->employee?->id;

        if (! $employeeId) {
            return false;
        }

        return DeliveryTeamMember::query()
            ->where('delivery_team_id', $member->delivery_team_id)
            ->where('employee_id', $employeeId)
            ->whereNull('left_at')
            ->exists();
    }

    /**
     * Add a member to a team.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Update membership information.
     */
    public function update(User $user, DeliveryTeamMember $member): bool
    {
        return $user->isAdmin();
    }

    /**
     * Leave own team.
     */
    public function leave(User $user, DeliveryTeamMember $member): bool
    {
        return $member->left_at === null
            && $user->employee?->id === $member->employee_id;
    }

    /**
     * Remove a member from a team.
     */
    public function remove(User $user, DeliveryTeamMember $member): bool
    {
        return $user->isAdmin()
            && $member->left_at === null;
    }

    /**
     * Delete membership record.
     */
    public function delete(User $user, DeliveryTeamMember $member): bool
    {
        return $user->isAdmin();
    }

    /**
     * Restore membership record.
     */
    public function restore(User $user, DeliveryTeamMember $member): bool
    {
        return $user->isAdmin();
    }

    /**
     * Permanently delete membership record.
     */
    public function forceDelete(User $user, DeliveryTeamMember $member): bool
    {
        return $user->isAdmin();
    }
}
