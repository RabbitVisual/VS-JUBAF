<?php

namespace Modules\Intercessor\App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Intercessor\App\Models\PrayerRequest;

class PrayerRequestApproved extends Notification
{
    use Queueable;

    public PrayerRequest $request;

    /**
     * Create a new notification instance.
     */
    public function __construct(PrayerRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'title' => 'Pedido de Oração Aprovado',
            'message' => 'Seu pedido de oração "' . $this->request->title . '" foi aprovado.',
            'link' => route('member.intercessor.requests.show', $this->request->id),
            'icon' => 'check-circle',
            'type' => 'success'
        ];
    }
}
