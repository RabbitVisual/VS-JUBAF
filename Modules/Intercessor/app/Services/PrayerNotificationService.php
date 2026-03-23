<?php

namespace Modules\Intercessor\App\Services;

use Illuminate\Support\Facades\Log;
use Modules\Intercessor\App\Models\PrayerCommitment;
use Modules\Intercessor\App\Models\PrayerRequest;

class PrayerNotificationService
{
    /**
     * Notify intercessors about a high/critical urgency request.
     */
    public function notifyUrgentRequest(PrayerRequest $request): void
    {
        if ($request->urgency_level !== 'critical') {
            return;
        }

        Log::info("Critical Prayer Request Created: {$request->title} (ID: {$request->id})");

        // Notify Admins and Intercessors (Jules' Improvement)
        $recipients = \App\Models\User::whereHas('role', function($q) {
            $q->whereIn('slug', ['admin', 'intercessor', 'prayer_team']);
        })->get();

        if ($recipients->isNotEmpty()) {
            \Illuminate\Support\Facades\Notification::send($recipients, new \Modules\Intercessor\App\Notifications\CriticalRequestNotification($request));
        }

        // Legacy dispatch (keep for backward compatibility if needed, but preferred to use Notification)
        // \Modules\Notifications\Jobs\SendUrgentPrayerEmailJob::dispatch($request);
    }

    /**
     * Notify the request author that someone prayed.
     */
    public function notifyCommitment(PrayerCommitment $commitment): void
    {
        $request = $commitment->request;
        $intercessor = $commitment->user;

        Log::info("User {$intercessor->name} committed to pray for request {$request->id}");

        // Dispatch Job
        \Modules\Notifications\Jobs\SendCommitmentEmailJob::dispatch($request, $intercessor);
    }
}
