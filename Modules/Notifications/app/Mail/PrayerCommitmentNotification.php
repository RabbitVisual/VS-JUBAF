<?php

namespace Modules\Notifications\App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PrayerCommitmentNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $prayerRequest;

    public $supportAgent;

    public $requestOwner;

    /**
     * Create a new message instance.
     */
    public function __construct(object $prayerRequest, User $supportAgent)
    {
        $this->prayerRequest = $prayerRequest;
        $this->supportAgent = $supportAgent;
        $this->requestOwner = $prayerRequest->user ?? null;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Novo Apoio Registrado - '.config('app.name'))
            ->view('notifications::mail.pastoral.commitment');
    }
}
