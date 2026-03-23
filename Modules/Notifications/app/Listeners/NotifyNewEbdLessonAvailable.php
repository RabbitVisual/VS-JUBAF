<?php

namespace Modules\Notifications\App\Listeners;

use Modules\EBD\App\Events\NewLessonAvailable;
use Modules\Notifications\App\Services\InAppNotificationService;

class NotifyNewEbdLessonAvailable
{
    public function __construct(
        protected InAppNotificationService $inApp
    ) {}

    public function handle(NewLessonAvailable $event): void
    {
        $lesson = $event->lesson;
        $lesson->load('class');

        $title = 'Nova lição da EBD';
        $message = "Lição \"{$lesson->title}\" disponível" . ($lesson->class ? " na classe {$lesson->class->name}" : '.');
        $actionUrl = null;
        if (function_exists('route') && (isset($lesson->id))) {
            try {
                $actionUrl = route('admin.ebd.lessons.show', $lesson);
            } catch (\Throwable $e) {
                // route may not exist
            }
        }

        $this->inApp->sendToAdmins($title, $message, [
            'type' => 'info',
            'action_url' => $actionUrl,
            'action_text' => 'Ver lição',
            'notification_type' => 'ebd_lesson',
        ]);
    }
}
