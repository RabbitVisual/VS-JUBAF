<?php

namespace Modules\Gamification\App\Listeners;

use Modules\Gamification\App\Events\MedalUnlocked;
use Modules\Notifications\App\Models\SystemNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendMedalUnlockedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(MedalUnlocked $event): void
    {
        $notification = SystemNotification::create([
            'title' => 'Medalha desbloqueada!',
            'message' => "Parabéns! Você conquistou a medalha '{$event->medal->title}'!",
            'type' => 'achievement',
            'priority' => 'normal',
            'target_users' => [$event->user->id],
            'target_roles' => null,
            'target_ministries' => null,
            'action_url' => route('memberpanel.cbav-bot.analysis'),
            'action_text' => 'Ver CBAV Bot',
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
