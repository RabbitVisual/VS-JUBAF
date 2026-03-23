<?php

namespace Modules\Notifications\App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Modules\Notifications\Mail\UrgentPrayerAlert;

class SendUrgentPrayerEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;

    /**
     * Create a new job instance.
     */
    public function __construct(object $request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Find targets: liderança administrativa.
        $recipients = User::whereHas('roles', function ($q) {
            $q->whereIn('name', [
                'Super Admin',
                'Presidente',
                'Vice-Presidente',
                'Secretário',
                'Tesoureiro',
                'Líder Local',
            ]);
        })->get(); // Get all for now. If list is huge, chunking is needed.

        foreach ($recipients as $recipient) {
            Mail::to($recipient->email)->queue(new UrgentPrayerAlert($this->request));
        }
    }
}
