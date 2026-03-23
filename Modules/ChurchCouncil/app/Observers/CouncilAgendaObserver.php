<?php

namespace Modules\ChurchCouncil\App\Observers;

use Modules\ChurchCouncil\App\Models\CouncilAgenda;
use Modules\ChurchCouncil\App\Models\CouncilMember;
use Modules\Notifications\App\Services\InAppNotificationService;

class CouncilAgendaObserver
{
    public function __construct(
        protected InAppNotificationService $inApp
    ) {}

    /**
     * Notify president/secretary when a new agenda item is added (optional: all council).
     */
    public function created(CouncilAgenda $agenda): void
    {
        $meeting = $agenda->meeting;
        if (! $meeting) {
            return;
        }
        $users = CouncilMember::active()
            ->whereIn('council_role', ['president', 'vice_president', 'secretary'])
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter();
        if ($users->isEmpty()) {
            $users = CouncilMember::active()->with('user')->get()->pluck('user')->filter();
        }
        if ($users->isEmpty()) {
            return;
        }
        $this->inApp->sendToUsers(
            $users,
            'Nova pauta adicionada',
            "Pauta \"{$agenda->title}\" na reunião \"{$meeting->title}\".",
            [
                'type' => 'info',
                'action_url' => url('/admin/conselho/reunioes/'.$meeting->id),
                'action_text' => 'Ver reunião',
            ]
        );
    }
}
