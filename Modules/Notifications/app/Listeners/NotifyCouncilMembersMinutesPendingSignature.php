<?php

namespace Modules\Notifications\App\Listeners;

use Modules\ChurchCouncil\App\Events\MinutesPendingSignature;
use Modules\ChurchCouncil\App\Models\CouncilMember;
use Modules\Notifications\App\Services\InAppNotificationService;

class NotifyCouncilMembersMinutesPendingSignature
{
    public function __construct(
        protected InAppNotificationService $inApp
    ) {}

    public function handle(MinutesPendingSignature $event): void
    {
        $meeting = $event->meeting;
        $councilUsers = CouncilMember::active()->with('user')->get()->pluck('user')->filter();

        if ($councilUsers->isEmpty()) {
            return;
        }

        $this->inApp->sendToUsers(
            $councilUsers,
            'Ata pendente de visto',
            "A ata da reunião de " . $meeting->scheduled_at?->format('d/m/Y') . " está disponível para seu visto digital.",
            [
                'type' => 'info',
                'priority' => 'normal',
                'action_url' => route('admin.churchcouncil.meetings.show', $meeting),
                'action_text' => 'Assinar ata',
                'notification_type' => 'churchcouncil_minutes',
            ]
        );
    }
}
