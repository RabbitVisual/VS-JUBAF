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
        $normalized = mb_strtolower($roleSlug);
        $roleNames = match ($normalized) {
            'admin' => ['Super Admin', 'Presidente'],
            'lideranca', 'liderança' => ['Super Admin', 'Presidente', 'Vice-Presidente', 'Secretário', 'Tesoureiro', 'Líder Local'],
            'membro', 'member' => ['Jovem'],
            default => [$roleSlug],
        };

        $users = User::whereHas('roles', function($query) use ($roleNames) {
            $query->whereIn('name', $roleNames);
        })->get();

        if ($users->isNotEmpty()) {
            $this->notifyMany($users, $notification);
        }
    }

    /**
     * Send a notification to all Admins and liderancas.
     *
     * @param Notification $notification
     * @return void
     */
    public function notifyAdmins(Notification $notification): void
    {
         $users = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['Super Admin', 'Presidente', 'Vice-Presidente', 'Secretário', 'Tesoureiro', 'Líder Local']);
        })->get();

        if ($users->isNotEmpty()) {
            $this->notifyMany($users, $notification);
        }
    }
}
