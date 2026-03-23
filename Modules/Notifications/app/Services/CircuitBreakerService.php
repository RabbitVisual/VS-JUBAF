<?php

namespace Modules\Notifications\App\Services;

use Modules\Notifications\App\Models\NotificationChannelStatus;

class CircuitBreakerService
{
    public function isOpen(string $channel, string $provider): bool
    {
        $status = NotificationChannelStatus::where('channel', $channel)
            ->where('provider', $provider)
            ->first();

        if (! $status) {
            return false;
        }

        return $status->isOpen();
    }

    public function recordFailure(string $channel, string $provider): void
    {
        $status = NotificationChannelStatus::firstOrCreate(
            ['channel' => $channel, 'provider' => $provider],
            ['failure_count' => 0]
        );

        $status->increment('failure_count');
        $status->refresh();
        $openMinutes = config('notifications.circuit_breaker.open_minutes', 15);
        $failureThreshold = config('notifications.circuit_breaker.failure_threshold', 3);
        $status->update([
            'last_failure_at' => now(),
            'open_until' => $status->failure_count >= $failureThreshold
                ? now()->addMinutes($openMinutes)
                : null,
        ]);
    }

    public function recordSuccess(string $channel, string $provider): void
    {
        NotificationChannelStatus::where('channel', $channel)
            ->where('provider', $provider)
            ->update([
                'failure_count' => 0,
                'open_until' => null,
            ]);
    }

    public function getOpenUntil(string $channel, string $provider): ?\Carbon\Carbon
    {
        $status = NotificationChannelStatus::where('channel', $channel)
            ->where('provider', $provider)
            ->first();

        return $status?->open_until;
    }
}
