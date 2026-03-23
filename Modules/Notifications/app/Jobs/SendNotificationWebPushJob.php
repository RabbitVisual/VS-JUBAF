<?php

namespace Modules\Notifications\App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Notifications\App\Models\NotificationAuditLog;
use Modules\Notifications\App\Models\NotificationFailedDelivery;
use Modules\Notifications\App\Models\SystemNotification;
use Modules\Notifications\App\Services\CircuitBreakerService;

class SendNotificationWebPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 600, 3600];

    public function __construct(
        public User $user,
        public SystemNotification $notification,
        public string $title,
        public string $body,
        public ?string $actionUrl = null
    ) {
        $this->onQueue(config('notifications.queue', 'default'));
    }

    public function handle(CircuitBreakerService $circuitBreaker): void
    {
        $provider = config('notifications.channels.webpush.provider', 'webpush');

        if ($circuitBreaker->isOpen('webpush', $provider)) {
            $this->moveToDlq('Circuit open for webpush/'.$provider);
            $this->alertAdminOnce('webpush', $provider);

            return;
        }

        try {
            // Placeholder: actual WebPush implementation would use a driver (Firebase, minishlink/laravel-web-push, etc.)
            // For now we only audit and do not send; integrate with your WebPush provider when ready.
            $circuitBreaker->recordSuccess('webpush', $provider);

            NotificationAuditLog::create([
                'user_id' => $this->user->id,
                'channel' => 'webpush',
                'status' => 'sent',
                'notification_id' => $this->notification->id,
                'payload' => ['title' => $this->title],
            ]);
        } catch (\Throwable $e) {
            Log::warning('SendNotificationWebPushJob failed: '.$e->getMessage());
            $circuitBreaker->recordFailure('webpush', $provider);

            NotificationAuditLog::create([
                'user_id' => $this->user->id,
                'channel' => 'webpush',
                'status' => 'failed',
                'notification_id' => $this->notification->id,
                'payload' => ['title' => $this->title],
                'error_message' => $e->getMessage(),
            ]);

            if ($this->attempts() >= $this->tries) {
                $this->moveToDlq($e->getMessage());
            }

            throw $e;
        }
    }

    protected function moveToDlq(string $errorMessage): void
    {
        NotificationFailedDelivery::create([
            'user_id' => $this->user->id,
            'notification_id' => $this->notification->id,
            'channel' => 'webpush',
            'provider' => config('notifications.channels.webpush.provider'),
            'error_message' => $errorMessage,
            'payload' => ['title' => $this->title],
            'attempts' => $this->attempts(),
            'last_attempt_at' => now(),
            'retry_pending' => false,
        ]);
    }

    protected function alertAdminOnce(string $channel, string $provider): void
    {
        if (! class_exists(\Modules\Notifications\App\Services\InAppNotificationService::class)) {
            return;
        }
        app(\Modules\Notifications\App\Services\InAppNotificationService::class)->sendToAdmins(
            'Canal de notificação indisponível',
            "O canal {$channel} (provedor: {$provider}) está com circuit breaker aberto.",
            ['type' => 'warning', 'priority' => 'high']
        );
    }
}
