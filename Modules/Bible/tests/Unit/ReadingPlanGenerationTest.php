<?php

namespace Modules\Bible\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Bible\App\Models\BiblePlan;
use Modules\Bible\App\Models\BiblePlanContent;
use Modules\Bible\App\Models\BiblePlanDay;
use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Models\Chapter;
use Modules\Bible\App\Models\BibleVersion;
use Modules\Bible\App\Services\PlanGeneratorEngine;
use Modules\Bible\App\Services\ReadingCatchUpService;
use Modules\Bible\App\Services\ReadingPlanGeneratorService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReadingPlanGenerationTest extends TestCase
{
    use RefreshDatabase;

    private BibleVersion $version;

    private int $expectedTotalVerses = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBibleStructure();
    }

    private function seedBibleStructure(): void
    {
        $this->version = BibleVersion::create([
            'name' => 'Test Almeida',
            'abbreviation' => 'TA',
            'is_active' => true,
        ]);

        $bookIds = [];
        $verseCount = 0;
        for ($b = 1; $b <= 5; $b++) {
            $book = Book::create([
                'bible_version_id' => $this->version->id,
                'name' => "Livro {$b}",
                'book_number' => $b,
                'abbreviation' => "L{$b}",
                'testament' => $b <= 3 ? 'old' : 'new',
                'order' => $b,
            ]);
            $bookIds[] = $book->id;
            for ($c = 1; $c <= 10; $c++) {
                $versesInChapter = 28 + ($c % 5);
                $chapter = Chapter::create([
                    'book_id' => $book->id,
                    'chapter_number' => $c,
                    'total_verses' => $versesInChapter,
                ]);
                $verseCount += $versesInChapter;
            }
        }
        $this->expectedTotalVerses = $verseCount;
    }

    #[Test]
    public function it_distributes_reading_balanced_over_365_days(): void
    {
        $plan = BiblePlan::create([
            'title' => 'Plano 1 Ano',
            'slug' => 'plano-1-ano-test',
            'type' => 'sequential',
            'duration_days' => 365,
            'is_active' => false,
        ]);

        $engine = app(PlanGeneratorEngine::class);
        $bookIds = Book::where('bible_version_id', $this->version->id)->orderBy('order')->pluck('id')->toArray();
        $engine->generateSequential($plan, $bookIds, 365);

        $plan->refresh();
        $plan->load(['days.contents']);
        $days = $plan->days->sortBy('day_number')->values();
        self::assertCount(365, $days, 'Deve existir exatamente 365 dias no plano.');

        $totalAssignedVerses = $this->sumVersesFromPlan($plan);
        self::assertSame($this->expectedTotalVerses, $totalAssignedVerses, 'Total de versículos distribuídos deve igualar o total da estrutura.');

        $versesPerDay = $this->versesPerDayFromPlan($plan);
        $avg = $totalAssignedVerses / 365;
        $maxAllowed = (int) ceil($avg * 1.5);
        $minAllowed = (int) floor($avg * 0.5);
        foreach ($versesPerDay as $dayNum => $count) {
            if ($count > 0) {
                self::assertLessThanOrEqual($maxAllowed, $count, "Dia {$dayNum} não deve exceder 1.5x a média.");
            }
            if ($count > 0 && $avg >= 2) {
                self::assertGreaterThanOrEqual($minAllowed, $count, "Dia {$dayNum} não deve ficar abaixo de 0.5x a média.");
            }
        }
    }

    #[Test]
    public function it_generates_standard_one_year_plan_with_chronological_order(): void
    {
        $plan = BiblePlan::create([
            'title' => 'Bíblia 1 Ano Cronológico',
            'slug' => 'biblica-1-ano-cronologico',
            'type' => 'chronological',
            'duration_days' => 365,
            'is_active' => false,
        ]);

        $generator = app(ReadingPlanGeneratorService::class);
        $generator->generate($plan, (int) $this->version->id, [
            'complexity' => ReadingPlanGeneratorService::COMPLEXITY_STANDARD,
            'order_type' => ReadingPlanGeneratorService::ORDER_CHRONOLOGICAL,
        ]);

        $days = $plan->days()->orderBy('day_number')->get();
        self::assertGreaterThanOrEqual(1, $days->count(), 'Plano deve ter pelo menos um dia gerado.');
        self::assertLessThanOrEqual(365, $days->count(), 'Plano não deve exceder 365 dias.');
        $totalVerses = $this->sumVersesFromPlan($plan->load(['days.contents']));
        self::assertSame($this->expectedTotalVerses, $totalVerses, 'Total de versículos deve coincidir com a estrutura.');
    }

    private function sumVersesFromPlan(BiblePlan $plan): int
    {
        $total = 0;
        foreach ($plan->days as $day) {
            $contents = $day->relationLoaded('contents') ? $day->contents->where('type', 'scripture') : $day->contents()->where('type', 'scripture')->get();
            foreach ($contents as $content) {
                $total += $this->verseCountOfContent($content);
            }
        }
        return $total;
    }

    private function verseCountOfContent(BiblePlanContent $content): int
    {
        $cs = (int) $content->chapter_start;
        $ce = (int) ($content->chapter_end ?? $cs);
        $vs = (int) $content->verse_start;
        $ve = (int) $content->verse_end;

        if ($cs === $ce) {
            return max(0, $ve - $vs + 1);
        }
        $sum = 0;
        for ($ch = $cs; $ch <= $ce; $ch++) {
            $chapter = Chapter::where('book_id', $content->book_id)->where('chapter_number', $ch)->first();
            $capVerses = $chapter ? (int) $chapter->total_verses : 25;
            if ($ch === $cs) {
                $sum += $capVerses - $vs + 1;
            } elseif ($ch === $ce) {
                $sum += $ve;
            } else {
                $sum += $capVerses;
            }
        }
        return $sum;
    }

    /** @return array<int, int> day_number => verse count */
    private function versesPerDayFromPlan(BiblePlan $plan): array
    {
        $out = [];
        foreach ($plan->days as $day) {
            $contents = $day->relationLoaded('contents') ? $day->contents->where('type', 'scripture') : $day->contents()->where('type', 'scripture')->get();
            $count = 0;
            foreach ($contents as $content) {
                $count += $this->verseCountOfContent($content);
            }
            $out[$day->day_number] = $count;
        }
        return $out;
    }

    #[Test]
    public function it_calculates_projected_end_date_for_366_day_plan_in_leap_year(): void
    {
        $startDate = \Carbon\Carbon::parse('2024-01-01');
        $endDate = ReadingCatchUpService::getProjectedEndDate($startDate, 366);
        self::assertSame('2024-12-31', $endDate->toDateString(), '366 dias a partir de 1º jan 2024 deve terminar em 31 dez 2024.');
        self::assertSame(366, (int) ($startDate->diffInDays($endDate) + 1));
    }

    #[Test]
    public function it_handles_subscription_start_on_february_29_without_exception(): void
    {
        $plan = BiblePlan::create([
            'title' => 'Plano Ano Bissexto',
            'slug' => 'plano-ano-bissexto',
            'type' => 'sequential',
            'duration_days' => 365,
            'is_active' => true,
        ]);
        $user = \App\Models\User::factory()->create();
        $subscription = \Modules\Bible\App\Models\BiblePlanSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'start_date' => '2024-02-29',
            'projected_end_date' => ReadingCatchUpService::getProjectedEndDate(\Carbon\Carbon::parse('2024-02-29'), 365)->toDateString(),
        ]);
        $catchUp = app(ReadingCatchUpService::class);
        $delay = $catchUp->getDelayDays($subscription);
        self::assertIsInt($delay);
        self::assertGreaterThanOrEqual(0, $delay);
        $endDate = \Carbon\Carbon::parse($subscription->projected_end_date);
        self::assertSame('2025-02-27', $endDate->toDateString(), '365 dias a partir de 29 fev 2024 termina em 27 fev 2025.');
    }
}
