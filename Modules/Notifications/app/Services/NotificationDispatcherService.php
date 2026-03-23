<?php

namespace Modules\Notifications\App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Modules\Notifications\App\Jobs\SendNotificationEmailJob;
use Modules\Notifications\App\Jobs\SendNotificationSmsJob;
use Modules\Notifications\App\Jobs\SendNotificationWebPushJob;
use Modules\Notifications\App\Models\NotificationAuditLog;
use Modules\Notifications\App\Models\NotificationTemplate;
use Modules\Notifications\App\Models\SystemNotification;
use Modules\Notifications\App\Models\UserNotification;
use Modules\Notifications\App\Models\UserNotificationPreference;

/**
 * Dispatches notifications to multiple channels (email, webpush, sms) based on user preferences and DND.
 * In-app is handled by InAppNotificationService; this service enqueues the rest.
 */
class NotificationDispatcherService
{
    public function __construct(
        protected InAppNotificationService $inApp,
        protected NotificationTemplateResolver $templateResolver
    ) {}

    /**
     * Dispatch to all channels (in-app already done by caller). Enqueues email/webpush/sms per user preferences and DND.
     *
     * @param  Collection<int, User>|User  $users  Single user or collection
     * @param  array  $options  type, priority, action_url, action_text, notification_type (key for preferences/template)
     */
    public function dispatchChannels(SystemNotification $notification, $users, string $title, string $message, array $options = []): void
    {
        $users = $users instanceof User ? collect([$users]) : $users;
        $notificationType = $options['notification_type'] ?? 'generic';

        foreach ($users as $user) {
            if (! $user instanceof User) {
                continue;
            }
            $prefs = $this->getPreferences($user, $notificationType, $notification);
            $channels = $prefs['channels'];
            $inDnd = $prefs['in_dnd'];

            $subject = $title;
            $body = $message;
            $template = $this->templateResolver->resolve($notificationType, ['title' => $title, 'message' => $message, 'action_url' => $options['action_url'] ?? null]);
            if ($template) {
                $subject = $template['subject'] ?? $title;
                $body = $template['body'] ?? $message;
            }

            if (in_array('email', $channels, true) && ! $inDnd) {
                SendNotificationEmailJob::dispatch($user, $notification, $subject, $body);
            }
            if (in_array('webpush', $channels, true) && ! $inDnd) {
                SendNotificationWebPushJob::dispatch($user, $notification, $title, $message, $options['action_url'] ?? null);
            }
            if (in_array('sms', $channels, true) && ! $inDnd) {
                SendNotificationSmsJob::dispatch($user, $notification, $message);
            }

            $this->auditInApp($user->id, $notification->id);
        }
    }

    /**
     * Preferências do usuário para o tipo de notificação. Notificações importantes (high/urgent) ignoram DND e usam todos os canais.
     */
    protected function getPreferences(User $user, string $notificationType, SystemNotification $notification): array
    {
        $isImportant = in_array($notification->priority, ['high', 'urgent'], true);

        $pref = UserNotificationPreference::where('user_id', $user->id)
            ->where('notification_type', $notificationType)
            ->first();

        $channels = $pref && is_array($pref->channels) ? $pref->channels : ['in_app'];
        $inDnd = $pref ? $pref->isInDndWindow() : false;

        if ($isImportant) {
            $channels = array_values(array_unique(array_merge($channels, ['in_app', 'email', 'webpush'])));
            $inDnd = false;
        }

        return [
            'channels' => $channels,
            'in_dnd' => $inDnd,
        ];
    }

    protected function auditInApp(int $userId, int $notificationId): void
    {
        NotificationAuditLog::create([
            'user_id' => $userId,
            'channel' => 'in_app',
            'status' => 'sent',
            'notification_id' => $notificationId,
            'payload' => [],
        ]);
    }
}
