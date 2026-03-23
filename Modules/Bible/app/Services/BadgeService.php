<?php

declare(strict_types=1);

namespace Modules\Bible\App\Services;

use Carbon\Carbon;
use Modules\Bible\App\Models\BiblePlanDay;
use Modules\Bible\App\Models\BiblePlanSubscription;
use Modules\Bible\App\Models\BibleUserBadge;
use Modules\Bible\App\Models\UserReadingLog;

/**
 * CBAV2026: Awards badges based on user_reading_logs after each successful complete().
 */
final class BadgeService
{
    private const CONSECUTIVE_DAYS_BEREANO = 7;

    private const TOTAL_DAYS_FIEL_PACTO = 30;

    private const TOTAL_DAYS_LEITOR_CORPO = 15;

    private const BEREANO_COOLDOWN_DAYS = 7;

    public function evaluateAfterCompletion(BiblePlanSubscription $subscription, BiblePlanDay $day): void
    {
        $subscription->load('plan');
        $this->maybeAwardBereanoDaSemana($subscription, (int) $day->day_number);
        $this->maybeAwardFielAoPacto($subscription);
        $this->maybeAwardLeitorDoCorpo($subscription);
    }

    private function maybeAwardBereanoDaSemana(BiblePlanSubscription $subscription, int $justCompletedDayNumber): void
    {
        $firstInWindow = $justCompletedDayNumber - self::CONSECUTIVE_DAYS_BEREANO + 1;
        if ($firstInWindow < 1) {
            return;
        }
        $dayNumbers = range($firstInWindow, $justCompletedDayNumber);
        $logs = UserReadingLog::where('subscription_id', $subscription->id)
            ->whereIn('day_number', $dayNumbers)
            ->orderBy('day_number')
            ->get();
        if ($logs->count() !== self::CONSECUTIVE_DAYS_BEREANO) {
            return;
        }
        $startDate = Carbon::parse($subscription->start_date)->startOfDay();
        $allOnTime = true;
        foreach ($dayNumbers as $dayNum) {
            $log = $logs->firstWhere('day_number', $dayNum);
            if (! $log) {
                $allOnTime = false;
                break;
            }
            $expectedDate = $startDate->copy()->addDays($dayNum - 1);
            $completedDate = Carbon::parse($log->completed_at)->startOfDay();
            if ($completedDate->gt($expectedDate)) {
                $allOnTime = false;
                break;
            }
        }
        if (! $allOnTime) {
            return;
        }
        $recentBadge = BibleUserBadge::where('subscription_id', $subscription->id)
            ->where('badge_key', BibleUserBadge::BADGE_BEREANO_SEMANA)
            ->where('awarded_at', '>=', now()->subDays(self::BEREANO_COOLDOWN_DAYS))
            ->exists();
        if ($recentBadge) {
            return;
        }
        BibleUserBadge::create([
            'user_id' => $subscription->user_id,
            'badge_key' => BibleUserBadge::BADGE_BEREANO_SEMANA,
            'subscription_id' => $subscription->id,
            'awarded_at' => now(),
        ]);
    }

    private function maybeAwardFielAoPacto(BiblePlanSubscription $subscription): void
    {
        $count = UserReadingLog::where('subscription_id', $subscription->id)->count();
        if ($count < self::TOTAL_DAYS_FIEL_PACTO) {
            return;
        }
        $exists = BibleUserBadge::where('subscription_id', $subscription->id)
            ->where('badge_key', BibleUserBadge::BADGE_FIEL_AO_PACTO)
            ->exists();
        if ($exists) {
            return;
        }
        BibleUserBadge::create([
            'user_id' => $subscription->user_id,
            'badge_key' => BibleUserBadge::BADGE_FIEL_AO_PACTO,
            'subscription_id' => $subscription->id,
            'awarded_at' => now(),
        ]);
    }

    private function maybeAwardLeitorDoCorpo(BiblePlanSubscription $subscription): void
    {
        if (! $subscription->plan->is_church_plan) {
            return;
        }
        $count = UserReadingLog::where('subscription_id', $subscription->id)->count();
        if ($count < self::TOTAL_DAYS_LEITOR_CORPO) {
            return;
        }
        $exists = BibleUserBadge::where('subscription_id', $subscription->id)
            ->where('badge_key', BibleUserBadge::BADGE_LEITOR_DO_CORPO)
            ->exists();
        if ($exists) {
            return;
        }
        BibleUserBadge::create([
            'user_id' => $subscription->user_id,
            'badge_key' => BibleUserBadge::BADGE_LEITOR_DO_CORPO,
            'subscription_id' => $subscription->id,
            'awarded_at' => now(),
        ]);
    }
}
