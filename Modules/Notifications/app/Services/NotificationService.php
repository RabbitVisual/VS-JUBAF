<?php

namespace Modules\Notifications\App\Services;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Send a notification to a specific user.
     *
     * @param User $user
     * @param Notification $notification
     * @return void
     */
    public function notifyUser(User $user, Notification $notification): void
    {
        $user->notify($notification);
    }

    /**
     * Send a notification to a group of users (e.g., by role or collection).
     *
     * @param mixed $users Collection of users or query
     * @param Notification $notification
     * @return void
     */
    public function notifyMany($users, Notification $notification): void
    {
        NotificationFacade::send($users, $notification);
    }

    /**
     * Send a notification to users with a specific role slug.
     *
     * @param string $roleSlug
     * @param Notification $notification
     * @return void
     */
    public function notifyRole(string $roleSlug, Notification $notification): void
    {
        $users = User::whereHas('role', function($query) use ($roleSlug) {
            $query->where('slug', $roleSlug);
        })->get();

        if ($users->isNotEmpty()) {
            $this->notifyMany($users, $notification);
        }
    }

    /**
     * Send a notification to all Admins and Pastors.
     *
     * @param Notification $notification
     * @return void
     */
    public function notifyAdmins(Notification $notification): void
    {
         $users = User::whereHas('role', function($query) {
            $query->whereIn('slug', ['admin', 'pastor']);
        })->get();

        if ($users->isNotEmpty()) {
            $this->notifyMany($users, $notification);
        }
    }
}
