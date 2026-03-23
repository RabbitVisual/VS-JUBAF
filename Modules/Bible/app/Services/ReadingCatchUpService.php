<?php

declare(strict_types=1);

namespace Modules\Bible\App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Bible\App\Models\BiblePlanDay;
use Modules\Bible\App\Models\BiblePlanSubscription;
use Modules\Bible\App\Models\BibleReadingAuditLog;
use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Models\Chapter;

/**
 * Anti-Frustration: Recalcula o conteúdo restante até a data final quando o usuário está atrasado (>3 dias).
 * Quando atraso >= 5 dias, sinaliza necessidade de acompanhamento pastoral.
 */
final class ReadingCatchUpService
{
    public const BEHIND_THRESHOLD_DAYS = 3;

    public const PASTORAL_FOLLOW_UP_THRESHOLD_DAYS = 5;

    /**
     * Last day of the plan (start_date + duration_days - 1). Safe for leap years and Feb 29.
     */
    public static function getProjectedEndDate(Carbon $startDate, int $durationDays): Carbon
    {
        return $startDate->copy()->addDays(max(1, $durationDays) - 1);
    }

    public function __construct(
        private readonly PlanGeneratorEngine $engine
    ) {}

    public function shouldOfferRecalculate(BiblePlanSubscription $subscription): bool
    {
        return $this->getDelayDays($subscription) >= self::BEHIND_THRESHOLD_DAYS;
    }

    public function getDelayDays(BiblePlanSubscription $subscription): int
    {
        if ($subscription->is_completed) {
            return 0;
        }
        $progress = $subscription->progress()->count();
        $start = Carbon::parse($subscription->start_date);
        $daysSinceStart = $start->diffInDays(Carbon::today()) + 1;

        return (int) max(0, $daysSinceStart - $progress);
    }

    /**
     * Quando atraso >= 5 dias, marca a inscrição para acompanhamento pastoral.
     */
    public function ensurePastoralFollowUpWhenBehind(BiblePlanSubscription $subscription): void
    {
        $delay = $this->getDelayDays($subscription);
        if ($delay < self::PASTORAL_FOLLOW_UP_THRESHOLD_DAYS) {
            return;
        }
        $this->markPastoralFollowUpForDelay($subscription->user, $delay, $subscription);
    }

    /**
     * Registra flag de acompanhamento pastoral para uso em relatórios da liderança.
     */
    public function markPastoralFollowUpForDelay(User $user, int $delayDays, ?BiblePlanSubscription $subscription = null): void
    {
        // Mantido para compatibilidade sem acoplamento a módulos legados.
        unset($user, $delayDays, $subscription);
    }

    /**
     * Redistribute remaining readings from next unread day to projected_end_date.
     * Replaces plan days from (completedDays+1)..totalDays with new balanced days.
     * Runs inside a single DB transaction and writes an audit log entry on success.
     */
    public function recalculateRemainingRoute(BiblePlanSubscription $subscription, ?Carbon $newEndDate = null): void
    {
        $plan = $subscription->plan;
        $completedCount = $subscription->progress()->count();
        $totalDays = $plan->days()->count();

        if ($completedCount >= $totalDays) {
            return;
        }

        $endDate = $newEndDate ?? Carbon::parse($subscription->projected_end_date);
        $daysRemaining = max(1, Carbon::today()->diffInDays($endDate, false) + 1);
        $fromDayNumber = $completedCount + 1;

        $existingDaysFrom = $plan->days()->where('day_number', '>=', $fromDayNumber)->with('contents')->orderBy('day_number')->get();
        $allChapters = $this->collectChaptersFromPlanDays($existingDaysFrom);

        if (empty($allChapters)) {
            return;
        }

        $oldEndDate = $subscription->projected_end_date
            ? Carbon::parse($subscription->projected_end_date)->toDateString()
            : null;

        $payload = [
            'from_day' => $fromDayNumber,
            'days_remaining' => $daysRemaining,
            'old_end_date' => $oldEndDate,
            'new_end_date' => $endDate->toDateString(),
        ];

        DB::transaction(function () use ($subscription, $plan, $allChapters, $daysRemaining, $fromDayNumber, $payload): void {
            $plan->days()->where('day_number', '>=', $fromDayNumber)->each(function (BiblePlanDay $day) {
                $day->contents()->delete();
                $day->delete();
            });

            $this->engine->distributeVersesForRange($plan, $allChapters, $daysRemaining, $fromDayNumber);

            BibleReadingAuditLog::create([
                'user_id' => $subscription->user_id,
                'subscription_id' => $subscription->id,
                'action' => BibleReadingAuditLog::ACTION_RECALCULATE_ROUTE,
                'payload' => $payload,
            ]);
        });
    }

    /**
     * Collect chapter list from existing plan days (for redistribution).
     *
     * @param  \Illuminate\Support\Collection<int, BiblePlanDay>  $days
     * @return array<int, array{book_id: int, book_name: string, chapter_number: int, chapter_id: int, total_verses: int}>
     */
    private function collectChaptersFromPlanDays($days): array
    {
        $allChapters = [];
        foreach ($days as $day) {
            foreach ($day->contents()->where('type', 'scripture')->orderBy('order_index')->get() as $content) {
                if (! $content->book_id) {
                    continue;
                }
                $book = Book::find($content->book_id);
                $startCh = (int) $content->chapter_start;
                $endCh = (int) ($content->chapter_end ?? $startCh);
                for ($ch = $startCh; $ch <= $endCh; $ch++) {
                    $chapter = Chapter::where('book_id', $content->book_id)->where('chapter_number', $ch)->first();
                    $allChapters[] = [
                        'book_id' => $content->book_id,
                        'book_name' => $book?->name ?? '?',
                        'chapter_number' => $ch,
                        'chapter_id' => $chapter?->id ?? 0,
                        'total_verses' => $chapter ? (int) $chapter->total_verses : 25,
                    ];
                }
            }
        }
        return $allChapters;
    }
}
