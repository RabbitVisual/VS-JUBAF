<?php

namespace Modules\Notifications\App\Listeners;

use Modules\EBD\App\Events\StudentLeveledUp;
use Modules\Notifications\App\Services\InAppNotificationService;

class SendStudentLeveledUpNotification
{
    public function __construct(
        protected InAppNotificationService $inApp
    ) {}

    public function handle(StudentLeveledUp $event): void
    {
        $user = $event->user;
        $level = $event->newLevel;

        $this->inApp->sendToUser($user, 'Level alcançado na EBD!', "Parabéns! Você subiu para o nível '{$level->name}' na Escola Bíblica.", [
            'type' => 'achievement',
            'priority' => 'normal',
            'action_url' => route('memberpanel.dashboard'),
            'action_text' => 'Ver painel',
        ]);
    }
}
