<?php

namespace Modules\Notifications\App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Modules\Notifications\Mail\PrayerCommitmentNotification;

class SendCommitmentEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;

    protected $supportAgent;

    /**
     * Create a new job instance.
     */
    public function __construct(object $request, User $supportAgent)
    {
        $this->request = $request;
        $this->supportAgent = $supportAgent;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $owner = $this->request->user ?? null;

        if ($owner && $owner->email) {
            Mail::to($owner->email)->queue(new PrayerCommitmentNotification($this->request, $this->supportAgent));
        }
    }
}
