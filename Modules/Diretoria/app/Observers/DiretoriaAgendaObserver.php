<?php

namespace Modules\Diretoria\App\Observers;

use Modules\Diretoria\App\Models\Pauta;
use Modules\Diretoria\App\Models\DiretoriaMember;
use Modules\Notifications\App\Services\InAppNotificationService;

class DiretoriaAgendaObserver
{
    public function __construct(
        protected InAppNotificationService $inApp
    ) {}

    /**
     * Notify president/secretary when a new agenda item is added (optional: all diretoria).
     */
    public function created(Pauta $agenda): void
    {
        $meeting = $agenda->meeting;
        if (! $meeting) {
            return;
        }
        $users = DiretoriaMember::active()
            ->whereIn('diretoria_role', ['president', 'vice_president', 'secretary'])
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter();
        if ($users->isEmpty()) {
            $users = DiretoriaMember::active()->with('user')->get()->pluck('user')->filter();
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
