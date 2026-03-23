<?php

namespace Modules\Intercessor\App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CriticalRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $request;

    public function __construct($request)
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
            ->subject('URGENTE: Novo Pedido de Oração Crítico')
            ->line('Um novo pedido de oração com prioridade CRÍTICA foi postado.')
            ->line('Título: ' . $this->request->title)
            ->action('Orar Agora', route('member.intercessor.room.show', $this->request->id));
    }

    public function toArray($notifiable): array
    {
        return [
            'request_id' => $this->request->id,
            'title' => 'Pedido Urgente!',
            'message' => 'Novo pedido crítico: ' . $this->request->title,
            'type' => 'critical'
        ];
    }
}
