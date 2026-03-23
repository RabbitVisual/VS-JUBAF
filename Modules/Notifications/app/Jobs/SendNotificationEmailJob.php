<?php

namespace Modules\Notifications\App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Notifications\App\Models\NotificationAuditLog;
use Modules\Notifications\App\Models\NotificationFailedDelivery;
use Modules\Notifications\App\Models\SystemNotification;
use Modules\Notifications\App\Services\CircuitBreakerService;

class SendNotificationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 600, 3600]; // 1 min, 10 min, 1 h

    public function __construct(
        public User $user,
        public SystemNotification $notification,
        public string $subject,
        public string $htmlBody
    ) {
        $this->onQueue(config('notifications.queue', 'default'));
    }

    public function handle(CircuitBreakerService $circuitBreaker): void
    {
        $provider = config('notifications.channels.mail.provider', 'smtp');

        if ($circuitBreaker->isOpen('mail', $provider)) {
            $this->moveToDlq('Circuit open for mail/'.$provider);
            $this->alertAdminOnce('mail', $provider);

            return;
        }

        try {
            Mail::html($this->htmlBody, function ($message) {
                $message->to($this->user->email, $this->user->name ?? null)
                    ->subject($this->subject);
            });

            $circuitBreaker->recordSuccess('mail', $provider);

            NotificationAuditLog::create([
                'user_id' => $this->user->id,
                'channel' => 'email',
                'status' => 'sent',
                'notification_id' => $this->notification->id,
                'payload' => ['subject' => $this->subject],
            ]);
        } catch (\Throwable $e) {
            Log::warning('SendNotificationEmailJob failed: '.$e->getMessage());
            $circuitBreaker->recordFailure('mail', $provider);

            NotificationAuditLog::create([
                'user_id' => $this->user->id,
                'channel' => 'email',
                'status' => 'failed',
                'notification_id' => $this->notification->id,
                'payload' => ['subject' => $this->subject],
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
            'channel' => 'email',
            'provider' => config('notifications.channels.mail.provider'),
            'error_message' => $errorMessage,
            'payload' => ['subject' => $this->subject],
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
            "O canal {$channel} (provedor: {$provider}) está com circuit breaker aberto. Notificações foram enviadas para a DLQ.",
            ['type' => 'warning', 'priority' => 'high']
        );
    }
}
