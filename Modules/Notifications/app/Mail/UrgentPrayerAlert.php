<?php

namespace Modules\Notifications\App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UrgentPrayerAlert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $request;

    /**
     * Create a new message instance.
     */
    public function __construct(object $request)
    {
        $this->request = $request;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('URGENTE: Novo Alerta de Cuidado Pastoral - '.config('app.name'))
            ->view('notifications::mail.pastoral.urgent-alert');
    }
}
