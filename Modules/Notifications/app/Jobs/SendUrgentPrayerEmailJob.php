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
use Modules\Notifications\Mail\UrgentPrayerAlert;

class SendUrgentPrayerEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;

    /**
     * Create a new job instance.
     */
    public function __construct(PrayerRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Find targets: Intercessors, Admins, Pastors.
        // Assuming roles are by slug.
        // A more optimized way would be User::whereHas('role', fn($q) => $q->whereIn('slug', [...]))->chunk(...)

        $recipients = User::whereHas('role', function ($q) {
            $q->whereIn('slug', ['intercessor', 'admin', 'pastor']);
        })->get(); // Get all for now. If list is huge, chunking is needed.

        foreach ($recipients as $recipient) {
            Mail::to($recipient->email)->queue(new UrgentPrayerAlert($this->request));
        }
    }
}
