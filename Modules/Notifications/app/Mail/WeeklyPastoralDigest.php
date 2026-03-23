<?php

namespace Modules\Notifications\App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WeeklyPastoralDigest extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $newRequestsCount;

    public $answeredCount;

    public $urgentRequests;

    public $testimonies;

    public function __construct($newRequestsCount, $answeredCount, $urgentRequests, $testimonies)
    {
        $this->newRequestsCount = $newRequestsCount;
        $this->answeredCount = $answeredCount;
        $this->urgentRequests = $urgentRequests;
        $this->testimonies = $testimonies;
    }

    public function build()
    {
        return $this->subject('Resumo Semanal de Acompanhamentos - '.config('app.name'))
            ->view('notifications::mail.pastoral.weekly-digest');
    }
}
