<?php

namespace Modules\Notifications\App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Modules\Intercessor\App\Models\PrayerRequest;
use Modules\Notifications\Mail\PrayerCommitmentNotification;

class SendCommitmentEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;

    protected $intercessor;

    /**
     * Create a new job instance.
     */
    public function __construct(PrayerRequest $request, User $intercessor)
    {
        $this->request = $request;
        $this->intercessor = $intercessor;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $owner = $this->request->user;

        if ($owner && $owner->email) {
            Mail::to($owner->email)->queue(new PrayerCommitmentNotification($this->request, $this->intercessor));
        }
    }
}
