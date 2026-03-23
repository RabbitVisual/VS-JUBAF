<?php

namespace Modules\Intercessor\App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Modules\Intercessor\App\Models\PrayerRequest;
use Modules\Notifications\Mail\WeeklyIntercessorDigest;

class SendWeeklyDigest extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'intercessor:send-weekly-digest';

    /**
     * The console command description.
     */
    protected $description = 'Send weekly prayer digest to intercessors';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Preparing Weekly Digest...');

        $startOfWeek = now()->subDays(7);

        // 1. Gather Data
        $newRequestsCount = PrayerRequest::where('created_at', '>=', $startOfWeek)->count();
        $answeredCount = PrayerRequest::where('status', 'answered')
            ->where('updated_at', '>=', $startOfWeek)
            ->count();

        $urgentRequests = PrayerRequest::active()
            ->where('urgency_level', '!=', 'normal')
            ->where('created_at', '>=', $startOfWeek)
            ->take(5)
            ->get();

        $testimonies = PrayerRequest::where('status', 'answered')
            ->where('updated_at', '>=', $startOfWeek)
            ->take(5)
            ->get();

        // Skip if nothing happened
        if ($newRequestsCount === 0 && $answeredCount === 0) {
            $this->info('No activity this week. Skipping digest.');

            return;
        }

        // 2. Find Recipients (Intercessors)
        $recipients = User::whereHas('role', function ($q) {
            $q->whereIn('slug', ['intercessor', 'admin', 'pastor']);
        })->get();

        $this->info("Found {$recipients->count()} recipients.");

        // 3. Send Emails
        foreach ($recipients as $recipient) {
            Mail::to($recipient->email)->queue(
                new WeeklyIntercessorDigest($newRequestsCount, $answeredCount, $urgentRequests, $testimonies)
            );
        }

        $this->info('Digest queued successfully!');
    }
}
