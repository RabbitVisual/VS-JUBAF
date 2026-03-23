<?php

namespace Modules\Intercessor\App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Intercessor\App\Models\PrayerRequest;

class NewPrayerRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $request;

    public function __construct(PrayerRequest $request)
    {
        $this->request = $request;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Novo Pedido de Oração: ' . $this->request->title)
            ->greeting('Olá ' . $notifiable->name . '!')
            ->line('Um novo pedido de oração requer sua atenção.')
            ->line('Título: ' . $this->request->title)
            ->line('Prioridade: ' . $this->request->urgency_label)
            ->action('Ver Pedido', route('member.intercessor.requests.index'))
            ->line('Que Deus abençoe seu ministério de intercessão.');
    }

    public function toArray($notifiable): array
    {
        return [
            'request_id' => $this->request->id,
            'title' => $this->request->title,
            'message' => 'Novo pedido de oração: ' . $this->request->title,
            'urgency' => $this->request->urgency_level,
            'action_url' => route('member.intercessor.requests.index'),
        ];
    }
}
