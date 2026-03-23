<?php

namespace Modules\Ministries\App\Services;

use Modules\Ministries\App\Models\MinistryReport;
use Modules\Ministries\App\Models\MinistryServicePoint;

class MinistryGamificationService
{
    /** Points awarded per volunteer when the ministry report is submitted (on time). */
    public const POINTS_PER_REPORT_SUBMISSION = 10;

    /**
     * Award service points to all active volunteers of the ministry when a report is submitted.
     * Idempotent: does not create duplicate points for the same user/ministry/period.
     */
    public function awardPointsForReport(MinistryReport $report): int
    {
        if ($report->status !== MinistryReport::STATUS_SUBMITTED) {
            return 0;
        }

        $ministry = $report->ministry;
        if (! $ministry) {
            return 0;
        }

        $activeMemberIds = $ministry->members()
            ->wherePivot('status', 'active')
            ->pluck('users.id');

        $year = (int) $report->report_year;
        $month = (int) $report->report_month;
        $points = self::POINTS_PER_REPORT_SUBMISSION;
        $awarded = 0;

        foreach ($activeMemberIds as $userId) {
            $exists = MinistryServicePoint::where('user_id', $userId)
                ->where('ministry_id', $ministry->id)
                ->where('period_year', $year)
                ->where('period_month', $month)
                ->exists();

            if (! $exists) {
                MinistryServicePoint::create([
                    'user_id' => $userId,
                    'ministry_id' => $ministry->id,
                    'points' => $points,
                    'ministry_report_id' => $report->id,
                    'period_year' => $year,
                    'period_month' => $month,
                ]);
                $awarded++;
            }
        }

        return $awarded;
    }
}
