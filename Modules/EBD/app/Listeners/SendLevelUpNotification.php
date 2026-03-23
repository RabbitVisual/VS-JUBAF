<?php

namespace Modules\EBD\App\Listeners;

use Modules\EBD\App\Events\StudentLeveledUp;
use Modules\Notifications\App\Services\InAppNotificationService;

class SendLevelUpNotification
{
    public function __construct(
        private InAppNotificationService $notificationService
    ) {}

    public function handle(StudentLeveledUp $event): void
    {
        $user = $event->user;
        $level = $event->newLevel;

        $this->notificationService->sendToUser(
            $user,
            'Parabéns! Novo Nível Alcançado!',
            "Você subiu para o nível {$level->level_number} - {$level->name}! Continue estudando a Palavra.",
            [
                'type' => 'achievement',
                'action_url' => route('memberpanel.ebd.arcade.leaderboard'),
                'action_text' => 'Ver meu progresso',
            ]
        );
    }
}
