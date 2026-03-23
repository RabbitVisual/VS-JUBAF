<?php

namespace Modules\Intercessor\App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RequestApprovedNotification extends Notification implements ShouldQueue
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
            ->subject('Seu pedido de oração foi aprovado!')
            ->line('Boas notícias! Seu pedido "' . $this->request->title . '" foi aprovado e já está visível para os intercessores.')
            ->action('Ver Pedido', route('member.intercessor.room.show', $this->request->id));
    }

    public function toArray($notifiable): array
    {
        return [
            'request_id' => $this->request->id,
            'title' => 'Pedido Aprovado',
            'message' => 'Seu pedido "' . $this->request->title . '" foi aprovado.',
            'type' => 'success'
        ];
    }
}
