<?php

namespace Modules\Notifications\App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Modules\Notifications\App\Events\NotificationCreated;
use Modules\Notifications\App\Models\UserNotificationPreference;
use Modules\Notifications\App\Services\NotificationDispatcherService;
use Modules\Notifications\App\Models\SystemNotification;
use Modules\Notifications\App\Models\UserNotification;

/**
 * Central service for in-app notifications (SystemNotification + UserNotification).
 * Use this from any module to send alerts, warnings, achievements without duplicating logic.
 *
 * Example:
 *   app(InAppNotificationService::class)->sendToUser($user, 'Título', 'Mensagem', ['type' => 'success']);
 *   app(InAppNotificationService::class)->sendToAdmins('Alerta', 'Algo requer atenção', ['priority' => 'high']);
 */
class InAppNotificationService
{
    /** Allowed notification types (must match system_notifications.type enum). */
    protected const TYPES = ['info', 'success', 'warning', 'error', 'achievement'];

    /** Allowed priorities. */
    protected const PRIORITIES = ['low', 'normal', 'high', 'urgent'];

    /**
     * Default options for notification creation.
     */
    protected function defaults(): array
    {
        return [
            'type' => 'info',
            'priority' => 'normal',
            'action_url' => null,
            'action_text' => null,
            'scheduled_at' => null,
            'expires_at' => null,
            'created_by' => auth()->id(),
            'broadcast' => true,
            'dispatch_multi_channel' => false,
            'notification_type' => 'generic',
        ];
    }

    protected function normalizeOptions(array $opts): array
    {
        $opts['type'] = in_array($opts['type'] ?? 'info', self::TYPES, true) ? $opts['type'] : 'info';
        $opts['priority'] = in_array($opts['priority'] ?? 'normal', self::PRIORITIES, true) ? $opts['priority'] : 'normal';
        return $opts;
    }

    /**
     * Send an in-app notification to a single user.
     * Creates SystemNotification + UserNotification and optionally broadcasts.
     * If notification_type is set (and not 'generic'), may update an existing notification
     * from the last 10 minutes instead of creating a new one (grouping/throttling).
     *
     * @param  array  $options  type, priority, action_url, action_text, notification_type, group_label, scheduled_at, expires_at, created_by, broadcast
     */
    public function sendToUser(User $user, string $title, string $message, array $options = []): SystemNotification
    {
        $opts = $this->normalizeOptions(array_merge($this->defaults(), $options));
        $notificationType = $opts['notification_type'] ?? 'generic';
        $groupLabel = $opts['group_label'] ?? null;

        if ($notificationType !== 'generic' && $this->maybeGroupWithExisting($user, $title, $message, $opts, $notificationType, $groupLabel)) {
            $existing = $this->findRecentForUserAndType($user->id, $notificationType);
            return $existing;
        }

        $notification = $this->createSystemNotification($title, $message, [
            'target_users' => [$user->id],
            'target_roles' => null,
            'target_ministries' => null,
        ], $opts);

        $this->attachUser($notification, $user->id, $opts['broadcast'] ? $user : null);

        if (! empty($opts['dispatch_multi_channel'])) {
            $this->dispatchMultiChannel($notification, collect([$user]), $title, $message, $opts);
        }

        return $notification;
    }

    /**
     * Find a system notification for this user and notification_type created in the last 10 minutes.
     */
    protected function findRecentForUserAndType(int $userId, string $notificationType): ?SystemNotification
    {
        $cutoff = now()->subMinutes(10);

        return SystemNotification::where('notification_type', $notificationType)
            ->whereJsonContains('target_users', $userId)
            ->where('created_at', '>=', $cutoff)
            ->orderByDesc('id')
            ->first();
    }

    /**
     * If there is a recent notification of the same type for this user, update it (increment count, message) and touch user pivot. Returns true if grouped.
     */
    protected function maybeGroupWithExisting(User $user, string $title, string $message, array $opts, string $notificationType, ?string $groupLabel): bool
    {
        $existing = $this->findRecentForUserAndType($user->id, $notificationType);
        if (! $existing) {
            return false;
        }

        $count = ($existing->group_count ?? 1) + 1;
        $existing->group_count = $count;
        $existing->message = $groupLabel
            ? "Você tem {$count} novos {$groupLabel}"
            : "Você tem {$count} novas atualizações.";
        $existing->save();

        $userNotif = UserNotification::where('user_id', $user->id)->where('notification_id', $existing->id)->first();
        if ($userNotif) {
            $userNotif->touch();
        }

        return true;
    }

    /**
     * Send an in-app notification to all admins/pastors.
     *
     * @param  array  $options  same as sendToUser
     */
    public function sendToAdmins(string $title, string $message, array $options = []): SystemNotification
    {
        $users = User::where('is_active', true)
            ->whereHas('role', fn ($q) => $q->whereIn('slug', ['admin', 'pastor']))
            ->get();

        return $this->sendToUsers($users, $title, $message, $options);
    }

    /**
     * Send an in-app notification to a collection of users.
     *
     * @param  Collection<int, User>  $users
     * @param  array  $options  same as sendToUser
     */
    public function sendToUsers(Collection $users, string $title, string $message, array $options = []): SystemNotification
    {
        if ($users->isEmpty()) {
            $opts = $this->normalizeOptions(array_merge($this->defaults(), $options));
            return $this->createSystemNotification($title, $message, [
                'target_users' => [],
                'target_roles' => null,
                'target_ministries' => null,
            ], $opts);
        }

        $opts = $this->normalizeOptions(array_merge($this->defaults(), $options));
        $notification = $this->createSystemNotification($title, $message, [
            'target_users' => $users->pluck('id')->values()->all(),
            'target_roles' => null,
            'target_ministries' => null,
        ], $opts);

        foreach ($users as $user) {
            $this->attachUser($notification, $user->id, $opts['broadcast'] ? $user : null);
        }

        if (! empty($opts['dispatch_multi_channel'])) {
            $this->dispatchMultiChannel($notification, $users, $title, $message, $opts);
        }

        return $notification;
    }

    /**
     * Send an in-app notification to users with a given role slug.
     *
     * @param  array  $options  same as sendToUser
     */
    public function sendToRole(string $roleSlug, string $title, string $message, array $options = []): SystemNotification
    {
        $users = User::where('is_active', true)
            ->whereHas('role', fn ($q) => $q->where('slug', $roleSlug))
            ->get();

        return $this->sendToUsers($users, $title, $message, $options);
    }

    /**
     * Create a SystemNotification record.
     */
    protected function createSystemNotification(string $title, string $message, array $targets, array $opts): SystemNotification
    {
        $data = [
            'title' => $title,
            'message' => $message,
            'type' => $opts['type'],
            'priority' => $opts['priority'],
            'target_users' => $targets['target_users'] ?? null,
            'target_roles' => $targets['target_roles'] ?? null,
            'target_ministries' => $targets['target_ministries'] ?? null,
            'action_url' => $opts['action_url'],
            'action_text' => $opts['action_text'],
            'scheduled_at' => $opts['scheduled_at'],
            'expires_at' => $opts['expires_at'],
            'is_read' => false,
            'created_by' => $opts['created_by'],
            'notification_type' => $opts['notification_type'] ?? null,
            'group_count' => 1,
        ];

        return SystemNotification::create($data);
    }

    /**
     * Attach one user to the notification (create UserNotification) and optionally broadcast.
     */
    protected function attachUser(SystemNotification $notification, int $userId, ?User $user = null): void
    {
        if (UserNotification::where('user_id', $userId)->where('notification_id', $notification->id)->exists()) {
            return;
        }

        UserNotification::create([
            'user_id' => $userId,
            'notification_id' => $notification->id,
            'is_read' => false,
        ]);

        if ($user && $this->shouldBroadcastToUser($user, $notification)) {
            event(new NotificationCreated($notification, $user));
        }
    }

    /**
     * Whether to broadcast (Pusher) to this user: only if in_app is enabled for this notification_type.
     * Notificações marcadas pelo admin como importantes (high/urgent) ignoram a preferência do usuário.
     */
    protected function shouldBroadcastToUser(User $user, SystemNotification $notification): bool
    {
        if (in_array($notification->priority, ['high', 'urgent'], true)) {
            return true;
        }
        $type = $notification->notification_type ?? 'generic';
        $pref = UserNotificationPreference::where('user_id', $user->id)
            ->where('notification_type', $type)
            ->first();
        $channels = $pref && is_array($pref->channels) ? $pref->channels : ['in_app'];
        return in_array('in_app', $channels, true);
    }

    protected function dispatchMultiChannel(SystemNotification $notification, Collection $users, string $title, string $message, array $opts): void
    {
        if (! class_exists(NotificationDispatcherService::class)) {
            return;
        }
        app(NotificationDispatcherService::class)->dispatchChannels($notification, $users, $title, $message, $opts);
    }
}
