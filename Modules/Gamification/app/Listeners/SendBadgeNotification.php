<?php

namespace Modules\Gamification\App\Listeners;

use Modules\Gamification\App\Events\BadgeAwarded;
use Modules\Notifications\App\Models\SystemNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendBadgeNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(BadgeAwarded $event): void
    {
        $notification = SystemNotification::create([
            'title' => 'Novo Badge Conquistado!',
            'message' => "Parabéns! Você conquistou o badge '{$event->badge->name}'!",
            'type' => 'achievement',
            'priority' => 'normal',
            'target_users' => [$event->user->id],
            'target_roles' => null,
            'target_ministries' => null,
            'action_url' => route('memberpanel.profile.show'),
            'action_text' => 'Ver Perfil',
            'scheduled_at' => null,
            'expires_at' => now()->addDays(30),
            'is_read' => false,
            'created_by' => $event->user->id,
        ]);

        $notification->users()->attach($event->user->id, [
            'is_read' => false,
            'read_at' => null,
        ]);
    }
}
