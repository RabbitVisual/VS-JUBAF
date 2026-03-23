<?php

namespace Modules\Worship\App\Services;

use Modules\Worship\App\Models\WorshipSetlist;
use Carbon\Carbon;

class RosterConflictService
{
    /**
     * Check if a user has a conflicting schedule.
     *
     * @param int $userId
     * @param Carbon $scheduledAt
     * @param int $durationHours Default 2 hours buffer
     * @return bool|WorshipSetlist Returns conflicting setlist or false
     */
    public function checkConflict(int $userId, Carbon $scheduledAt, int $durationHours = 2)
    {
        $start = $scheduledAt->copy()->subHours($durationHours);
        $end = $scheduledAt->copy()->addHours($durationHours);

        // Find setlists where user is rostered
        $conflict = WorshipSetlist::whereHas('roster', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->whereIn('status', ['pending', 'confirmed']); // Only active rosters
            })
            ->whereBetween('scheduled_at', [$start, $end])
            ->first();

        return $conflict;
    }
}
