<?php

namespace Modules\Notifications\App\Listeners;

use Modules\Diretoria\App\Events\MinutesPendingSignature;
use Modules\Diretoria\App\Models\DiretoriaMember;
use Modules\Notifications\App\Services\InAppNotificationService;

class NotifyDiretoriaMembersMinutesPendingSignature
{
    public function __construct(
        protected InAppNotificationService $inApp
    ) {}

    public function handle(MinutesPendingSignature $event): void
    {
        $meeting = $event->meeting;
        $diretoriaUsers = DiretoriaMember::active()->with('user')->get()->pluck('user')->filter();

        if ($diretoriaUsers->isEmpty()) {
            return;
        }

        $this->inApp->sendToUsers(
            $diretoriaUsers,
            'Ata pendente de visto',
            "A ata da reunião de " . $meeting->scheduled_at?->format('d/m/Y') . " está disponível para seu visto digital.",
            [
                'type' => 'info',
                'priority' => 'normal',
                'action_url' => route('admin.Diretoria.meetings.show', $meeting),
                'action_text' => 'Assinar ata',
                'notification_type' => 'Diretoria_minutes',
            ]
        );
    }
}
