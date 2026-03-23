<?php

namespace Modules\Sermons\App\Policies;

use App\Models\User;
use Modules\Sermons\App\Models\Sermon;

class SermonPolicy
{
    /**
     * Determine whether the user can view the sermon.
     */
    public function view(User $user, Sermon $sermon): bool
    {
        return $sermon->canView($user);
    }

    /**
     * Determine whether the user can update the sermon.
     */
    public function update(User $user, Sermon $sermon): bool
    {
        return $sermon->canEdit($user);
    }

    /**
     * Determine whether the user can delete the sermon (owner or admin only).
     */
    public function delete(User $user, Sermon $sermon): bool
    {
        return $sermon->canDelete($user);
    }
}
