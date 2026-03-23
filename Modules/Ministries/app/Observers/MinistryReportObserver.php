<?php

namespace Modules\Ministries\App\Observers;

use Modules\Ministries\App\Models\MinistryReport;
use Modules\Ministries\App\Services\MinistryGamificationService;

class MinistryReportObserver
{
    public function updated(MinistryReport $report): void
    {
        if ($report->isDirty('status') && $report->status === MinistryReport::STATUS_SUBMITTED) {
            app(MinistryGamificationService::class)->awardPointsForReport($report);
        }
    }

    public function created(MinistryReport $report): void
    {
        if ($report->status === MinistryReport::STATUS_SUBMITTED) {
            app(MinistryGamificationService::class)->awardPointsForReport($report);
        }
    }
}
