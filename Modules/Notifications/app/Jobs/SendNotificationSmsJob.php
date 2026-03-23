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

class SendNotificationSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 600, 3600];

    public function __construct(
        public User $user,
        public SystemNotification $notification,
        public string $message
    ) {
        $this->onQueue(config('notifications.queue', 'default'));
    }

    public function handle(CircuitBreakerService $circuitBreaker): void
    {
        $provider = config('notifications.channels.sms.provider', 'twilio');

        if ($circuitBreaker->isOpen('sms', $provider)) {
            $this->moveToDlq('Circuit open for sms/'.$provider);
            $this->alertAdminOnce('sms', $provider);

            return;
        }

        try {
            // Placeholder: actual SMS/WhatsApp implementation would use Twilio, Evolution API, etc.
            $circuitBreaker->recordSuccess('sms', $provider);

            NotificationAuditLog::create([
                'user_id' => $this->user->id,
                'channel' => 'sms',
                'status' => 'sent',
                'notification_id' => $this->notification->id,
                'payload' => ['message_length' => strlen($this->message)],
            ]);
        } catch (\Throwable $e) {
            Log::warning('SendNotificationSmsJob failed: '.$e->getMessage());
            $circuitBreaker->recordFailure('sms', $provider);

            NotificationAuditLog::create([
                'user_id' => $this->user->id,
                'channel' => 'sms',
                'status' => 'failed',
                'notification_id' => $this->notification->id,
                'payload' => [],
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
            'channel' => 'sms',
            'provider' => config('notifications.channels.sms.provider'),
            'error_message' => $errorMessage,
            'payload' => [],
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
