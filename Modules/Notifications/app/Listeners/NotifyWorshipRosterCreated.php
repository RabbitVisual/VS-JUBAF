<?php

namespace Modules\Notifications\App\Listeners;

use Modules\Notifications\App\Services\InAppNotificationService;
use Modules\Worship\App\Events\RosterCreated;

class NotifyWorshipRosterCreated
{
    public function __construct(
        protected InAppNotificationService $inApp
    ) {}

    public function handle(RosterCreated $event): void
    {
        $roster = $event->roster;
        $roster->load(['user', 'setlist', 'instrument']);

        $user = $roster->user;
        if (! $user) {
            return;
        }

        $setlist = $roster->setlist;
        $date = $setlist?->scheduled_at?->format('d/m/Y');
        $instrument = $roster->instrument?->name ?? 'instrumento';

        $title = 'Você foi escalado';
        $message = "Você foi escalado para {$instrument}" . ($date ? " no dia {$date}" : '.');
        $actionUrl = null;
        try {
            if (function_exists('route')) {
                $actionUrl = route('memberpanel.worship.my-rosters.index');
            }
        } catch (\Throwable $e) {
        }

        $this->inApp->sendToUser($user, $title, $message, [
            'type' => 'info',
            'action_url' => $actionUrl,
            'action_text' => 'Ver minhas escalas',
            'notification_type' => 'worship_roster',
        ]);
    }
}
