<?php

namespace Modules\Notifications\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $systemNotification = $this->notification;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'notification_id' => $this->notification_id,
            'is_read' => $this->is_read,
            'read_at' => $this->read_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'notification' => $systemNotification ? [
                'id' => $systemNotification->id,
                'title' => $systemNotification->title,
                'message' => $systemNotification->message,
                'type' => $systemNotification->type,
                'priority' => $systemNotification->priority,
                'notification_type' => $systemNotification->notification_type,
                'action_url' => $systemNotification->action_url,
                'action_text' => $systemNotification->action_text,
                'created_at' => $systemNotification->created_at->toIso8601String(),
            ] : null,
        ];
    }
}
