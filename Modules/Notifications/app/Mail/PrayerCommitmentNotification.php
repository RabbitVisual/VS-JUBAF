<?php

namespace Modules\Notifications\App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Intercessor\App\Models\PrayerRequest;

class PrayerCommitmentNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $prayerRequest;

    public $intercessor;

    public $requestOwner;

    /**
     * Create a new message instance.
     */
    public function __construct(PrayerRequest $prayerRequest, User $intercessor)
    {
        $this->prayerRequest = $prayerRequest;
        $this->intercessor = $intercessor;
        $this->requestOwner = $prayerRequest->user;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Novo Compromisso de Oração - '.config('app.name'))
            ->view('notifications::mail.intercessor.commitment');
    }
}
