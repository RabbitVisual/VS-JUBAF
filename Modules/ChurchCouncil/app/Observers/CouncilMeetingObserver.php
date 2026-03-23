<?php

namespace Modules\ChurchCouncil\App\Observers;

use Modules\ChurchCouncil\App\Models\CouncilMeeting;
use Modules\ChurchCouncil\App\Models\CouncilMember;
use Modules\Notifications\App\Services\InAppNotificationService;

class CouncilMeetingObserver
{
    public function __construct(
        protected InAppNotificationService $inApp
    ) {}

    /**
     * Notify council members when a new meeting is scheduled.
     */
    public function created(CouncilMeeting $meeting): void
    {
        if ($meeting->status !== CouncilMeeting::STATUS_SCHEDULED) {
            return;
        }
        $users = CouncilMember::active()->with('user')->get()->pluck('user')->filter();
        if ($users->isEmpty()) {
            return;
        }
        $date = $meeting->scheduled_date?->format('d/m/Y H:i') ?? 'a definir';
        $this->inApp->sendToUsers(
            $users,
            'Nova reunião do conselho',
            "Reunião \"{$meeting->title}\" agendada para {$date}.",
            [
                'type' => 'info',
                'action_url' => url('/admin/conselho/reunioes/'.$meeting->id),
                'action_text' => 'Ver reunião',
            ]
        );
    }
}
