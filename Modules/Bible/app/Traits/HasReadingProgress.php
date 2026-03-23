<?php

namespace Modules\Bible\App\Traits;

use Modules\Bible\App\Models\BiblePlanSubscription;
use Modules\Bible\Services\ReadingProgressService;

/**
 * Trait for Member (User) to check reading plan status in real time.
 */
trait HasReadingProgress
{
    public function biblePlanSubscriptions()
    {
        return $this->hasMany(BiblePlanSubscription::class, 'user_id');
    }

    public function getReadingProgress(int $planId): ?array
    {
        $service = app(ReadingProgressService::class);

        return $service->getProgress($this, $planId);
    }

    public function getActiveReadingSubscription(): ?BiblePlanSubscription
    {
        return BiblePlanSubscription::where('user_id', $this->id)
            ->where('is_completed', false)
            ->latest()
            ->first();
    }

    public function isDayCompleted(int $subscriptionId, int $planDayId): bool
    {
        return \Modules\Bible\App\Models\BibleUserProgress::where('subscription_id', $subscriptionId)
            ->where('plan_day_id', $planDayId)
            ->exists();
    }
}
