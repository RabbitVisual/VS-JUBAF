<?php

namespace Modules\Events\App\Listeners;

use Modules\Events\App\Events\RegistrationConfirmed;
use Modules\Notifications\App\Services\InAppNotificationService;

/**
 * When an event registration is confirmed, check if event has reached 80% capacity
 * and notify admins so they can monitor capacity.
 */
class NotifyAdminsOnEventCapacity
{
    public function __construct(
        protected InAppNotificationService $inAppNotificationService
    ) {}

    public function handle(RegistrationConfirmed $event): void
    {
        $registration = $event->registration;
        $ev = $registration->event;

        if ($ev->capacity === null || $ev->capacity < 1) {
            return;
        }

        $totalParticipants = $ev->confirmedRegistrations()
            ->withCount('participants')
            ->get()
            ->sum('participants_count');

        $percent = (int) round(($totalParticipants / $ev->capacity) * 100);
        if ($percent < 80) {
            return;
        }

        $title = __('events::messages.capacity_alert_title', ['percent' => $percent]) ?? 'Evento atingiu '.$percent.'% da capacidade';
        $message = __('events::messages.capacity_alert_message', [
            'event' => $ev->title,
            'percent' => $percent,
            'current' => $totalParticipants,
            'capacity' => $ev->capacity,
        ]) ?? "O evento \"{$ev->title}\" atingiu {$percent}% da capacidade ({$totalParticipants}/{$ev->capacity} participantes).";

        $this->inAppNotificationService->sendToAdmins($title, $message, [
            'type' => $percent >= 100 ? 'warning' : 'info',
            'priority' => $percent >= 100 ? 'high' : 'normal',
            'action_url' => route('admin.events.events.show', $ev),
            'action_text' => __('events::messages.view_event') ?? 'Ver evento',
        ]);
    }
}
