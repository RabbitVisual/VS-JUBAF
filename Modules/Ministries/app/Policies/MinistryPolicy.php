<?php

namespace Modules\Ministries\App\Policies;

use App\Models\User;
use Modules\Ministries\App\Models\Ministry;

class MinistryPolicy
{
    /**
     * Determine whether the user can view any ministries.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAdminAccess()
            || $user->councilMember()->where('is_active', true)->exists();
    }

    /**
     * Determine whether the user can view the ministry.
     */
    public function view(User $user, Ministry $ministry): bool
    {
        if ($user->hasAdminAccess()) {
            return true;
        }
        if ($user->councilMember()->where('is_active', true)->exists()) {
            return true;
        }
        return $ministry->hasMember($user) || $ministry->isLeader($user);
    }

    /**
     * Determine whether the user can create ministries.
     */
    public function create(User $user): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can update the ministry.
     */
    public function update(User $user, Ministry $ministry): bool
    {
        if ($user->hasAdminAccess()) {
            return true;
        }
        return $ministry->isLeader($user);
    }

    /**
     * Determine whether the user can delete the ministry.
     */
    public function delete(User $user, Ministry $ministry): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can manage members (add, remove, approve).
     */
    public function manageMembers(User $user, Ministry $ministry): bool
    {
        if ($user->hasAdminAccess()) {
            return true;
        }
        return $ministry->isLeader($user);
    }

    /**
     * Determine whether the user can submit plans for the ministry.
     */
    public function submitPlan(User $user, Ministry $ministry): bool
    {
        if ($user->hasAdminAccess()) {
            return true;
        }
        return $ministry->isLeader($user);
    }

    /**
     * Determine whether the user can submit reports for the ministry.
     */
    public function submitReport(User $user, Ministry $ministry): bool
    {
        if ($user->hasAdminAccess()) {
            return true;
        }
        return $ministry->isLeader($user);
    }
}
