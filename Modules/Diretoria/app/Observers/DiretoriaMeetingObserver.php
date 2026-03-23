<?php

namespace Modules\Diretoria\App\Observers;

use Modules\Diretoria\App\Models\diretoriaMeeting;
use Modules\Diretoria\App\Models\diretoriaMember;
use Modules\Notifications\App\Services\InAppNotificationService;

class diretoriaMeetingObserver
{
    public function __construct(
        protected InAppNotificationService $inApp
    ) {}

    /**
     * Notify diretoria members when a new meeting is scheduled.
     */
    public function created(diretoriaMeeting $meeting): void
    {
        if ($meeting->status !== diretoriaMeeting::STATUS_SCHEDULED) {
            return;
        }
        $users = diretoriaMember::active()->with('user')->get()->pluck('user')->filter();
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
