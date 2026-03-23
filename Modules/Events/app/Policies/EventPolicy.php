<?php

namespace Modules\Events\App\Policies;

use App\Models\User;
use Modules\Events\App\Models\Event;

class EventPolicy
{
    /**
     * Determine whether the user can view any events.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can view the event.
     */
    public function view(User $user, Event $event): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can create events.
     */
    public function create(User $user): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can update the event.
     */
    public function update(User $user, Event $event): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can delete the event.
     */
    public function delete(User $user, Event $event): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can manage registrations (list, confirm, cancel, export).
     */
    public function manageRegistrations(User $user, Event $event): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can access check-in.
     *
     * @param  mixed  $event  Optional (e.g. Event::class when authorizing without a specific event).
     */
    public function checkin(User $user, mixed $event = null): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can export (PDF, Excel, badges).
     */
    public function export(User $user, Event $event): bool
    {
        return $user->hasAdminAccess();
    }
}
