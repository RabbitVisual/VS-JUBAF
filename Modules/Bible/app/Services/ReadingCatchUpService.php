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
 * When delay >= 5 days, can create a private prayer request in Intercessor for "Disciplina e Deleite na Palavra".
 */
final class ReadingCatchUpService
{
    public const BEHIND_THRESHOLD_DAYS = 3;

    public const PRAYER_REQUEST_THRESHOLD_DAYS = 5;

    public const PRAYER_REQUEST_CATEGORY_NAME = 'Disciplina na Palavra';

    public const PRAYER_REQUEST_RECENT_DAYS = 7;

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
     * When user is behind by >= 5 days, ensure a prayer request exists (once per subscription, no duplicate in recent days).
     */
    public function ensurePrayerRequestForDelayWhenBehind(BiblePlanSubscription $subscription): void
    {
        $delay = $this->getDelayDays($subscription);
        if ($delay < self::PRAYER_REQUEST_THRESHOLD_DAYS) {
            return;
        }
        $this->createPrayerRequestForDelay($subscription->user, $delay, $subscription);
    }

    /**
     * Create a private prayer request in Intercessor for "Disciplina e Deleite na Palavra" (pastoral_only).
     * No-op if Intercessor is not available or user already has an active request in this category
     * (avoids overloading the pastor with multiple requests for the same unresolved issue).
     * When $subscription is provided, stores the created request id on the subscription for pastoral report linking.
     */
    public function createPrayerRequestForDelay(User $user, int $delayDays, ?BiblePlanSubscription $subscription = null): void
    {
        if (! class_exists(\Modules\Intercessor\App\Models\PrayerRequest::class)) {
            return;
        }
        $category = \Modules\Intercessor\App\Models\PrayerCategory::where('name', self::PRAYER_REQUEST_CATEGORY_NAME)->first();
        if (! $category) {
            return;
        }
        $hasActive = \Modules\Intercessor\App\Models\PrayerRequest::where('user_id', $user->id)
            ->where('category_id', $category->id)
            ->whereIn('status', ['active', 'pending'])
            ->exists();
        if ($hasActive) {
            return;
        }
        $request = \Modules\Intercessor\App\Models\PrayerRequest::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Disciplina e Deleite na Palavra',
            'description' => 'Peço oração por disciplina e deleite na leitura da Bíblia. Estou atrasado(a) no plano de leitura e quero retomar com fidelidade.',
            'privacy_level' => 'pastoral_only',
            'urgency_level' => 'normal',
            'is_anonymous' => false,
            'status' => 'active',
        ]);
        if ($subscription !== null) {
            $subscription->update(['prayer_request_id' => $request->id]);
        }
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
