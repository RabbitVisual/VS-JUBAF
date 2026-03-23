<?php

namespace Modules\Notifications\App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WeeklyIntercessorDigest extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $newRequestsCount;

    public $answeredCount;

    public $urgentRequests;

    public $testimonies;

    /**
     * Create a new message instance.
     */
    public function __construct($newRequestsCount, $answeredCount, $urgentRequests, $testimonies)
    {
        $this->newRequestsCount = $newRequestsCount;
        $this->answeredCount = $answeredCount;
        $this->urgentRequests = $urgentRequests;
        $this->testimonies = $testimonies;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Resumo Semanal de Oração - '.config('app.name'))
            ->view('notifications::mail.intercessor.weekly-digest');
    }
}
