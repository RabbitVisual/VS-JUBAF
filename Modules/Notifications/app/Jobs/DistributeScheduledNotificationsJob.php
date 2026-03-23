<?php

namespace Modules\Notifications\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Notifications\App\Models\SystemNotification;
use Modules\Notifications\App\Models\UserNotification;
use App\Models\User;

class DistributeScheduledNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Run: notificações com scheduled_at <= now() que ainda não foram distribuídas (0 user_notifications).
     */
    public function handle(): void
    {
        $now = now();

        SystemNotification::whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', $now)
            ->whereDoesntHave('users')
            ->each(function (SystemNotification $notification) {
                $users = $this->getTargetUsers($notification);
                foreach ($users as $user) {
                    if (! UserNotification::where('user_id', $user->id)->where('notification_id', $notification->id)->exists()) {
                        UserNotification::create([
                            'user_id' => $user->id,
                            'notification_id' => $notification->id,
                            'is_read' => false,
                        ]);
                        event(new \Modules\Notifications\App\Events\NotificationCreated($notification, $user));
                    }
                }
            });
    }

    private function getTargetUsers(SystemNotification $notification): \Illuminate\Database\Eloquent\Collection
    {
        $query = User::where('is_active', true);

        if (! empty($notification->target_users)) {
            return $query->whereIn('id', $notification->target_users)->get();
        }
        if (! empty($notification->target_roles)) {
            $query->whereHas('role', fn ($q) => $q->whereIn('slug', $notification->target_roles));
        }
        if (! empty($notification->target_ministries)) {
            $query->whereHas('ministries', fn ($q) => $q->whereIn('ministries.id', $notification->target_ministries)->wherePivot('status', 'active'));
        }

        return $query->get();
    }
}
