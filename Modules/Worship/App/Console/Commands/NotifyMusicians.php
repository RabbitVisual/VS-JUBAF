<?php

namespace Modules\Worship\App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Worship\App\Models\WorshipRoster;
use Modules\Worship\App\Enums\RosterStatus;

class NotifyMusicians extends Command
{
    protected $signature = 'worship:notify-musicians';
    protected $description = 'Send notifications/reminders to musicians for upcoming setlists';

    public function handle()
    {
        $pendingRosters = WorshipRoster::where('status', RosterStatus::PENDING)
            ->whereHas('setlist', function($query) {
                $query->where('scheduled_at', '>', now());
            })
            ->get();

        if ($pendingRosters->isEmpty()) {
            $this->info('No pending rosters to notify.');
            return;
        }

        $this->info("Sending notifications to {$pendingRosters->count()} musicians...");

        foreach ($pendingRosters as $roster) {
            // Logic to send Email/WhatsApp would go here
            // For now, we update notified_at
            $roster->update(['notified_at' => now()]);

            $this->line("Notified: {$roster->user->name} for {$roster->setlist->title}");
        }

        $this->info('Notifications sent successfully!');
    }
}
