<?php

namespace Modules\Diretoria\App\Observers;

use Modules\Diretoria\App\Models\Reuniao;
use Modules\Diretoria\App\Models\DiretoriaMember;
use Modules\Notifications\App\Services\InAppNotificationService;

class DiretoriaMeetingObserver
{
    public function __construct(
        protected InAppNotificationService $inApp
    ) {}

    /**
     * Notify diretoria members when a new meeting is scheduled.
     */
    public function created(Reuniao $meeting): void
    {
        if ($meeting->status !== Reuniao::STATUS_SCHEDULED) {
            return;
        }
        $users = DiretoriaMember::active()->with('user')->get()->pluck('user')->filter();
        if ($users->isEmpty()) {
            return;
        }
        $date = $meeting->scheduled_date?->format('d/m/Y H:i') ?? 'a definir';
        $this->inApp->sendToUsers(
            $users,
            'Nova reunião da diretoria',
            "Reunião \"{$meeting->title}\" agendada para {$date}.",
            [
                'type' => 'info',
                'action_url' => url('/admin/conselho/reunioes/'.$meeting->id),
                'action_text' => 'Ver reunião',
            ]
        );
    }
}
