<?php

namespace Modules\Intercessor\App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewCommitmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $request;
    protected $intercessor;

    public function __construct($request, $intercessor)
    {
        $this->request = $request;
        $this->intercessor = $intercessor;
    }

    public function via($notifiable): array
    {
        return ['database']; // Maybe not mail to avoid spam?
    }

    public function toArray($notifiable): array
    {
        return [
            'request_id' => $this->request->id,
            'title' => 'Alguém está orando por você!',
            'message' => $this->intercessor->name . ' comprometeu-se a orar pelo seu pedido "' . $this->request->title . '".',
            'type' => 'info'
        ];
    }
}
