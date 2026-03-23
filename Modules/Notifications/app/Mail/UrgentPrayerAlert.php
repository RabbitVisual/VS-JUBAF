<?php

namespace Modules\Notifications\App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Intercessor\App\Models\PrayerRequest;

class UrgentPrayerAlert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $request;

    /**
     * Create a new message instance.
     */
    public function __construct(PrayerRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('URGENTE: Novo Pedido de Oração - '.config('app.name'))
            ->view('notifications::mail.intercessor.urgent-alert');
    }
}
