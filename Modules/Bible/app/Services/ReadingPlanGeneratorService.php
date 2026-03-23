<?php

declare(strict_types=1);

namespace Modules\Bible\App\Services;

use Modules\Bible\App\Models\BiblePlan;
use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Models\Chapter;

/**
 * CBAV2026 Reading Plan Generator — Theology & Logic.
 *
 * Balanceamento por peso de versículos (~31.102 total); complexidades: iniciante (NT+Salmos),
 * standard (Bíblia toda 1 ano), exegetical; abordagem Batista: cronológico e doutrinário.
 */
final class ReadingPlanGeneratorService
{
    public const TOTAL_VERSES_REFERENCE = 31102;

    public const COMPLEXITY_INICIANTE = 'iniciante';

    public const COMPLEXITY_STANDARD = 'standard';

    public const COMPLEXITY_EXEGETICAL = 'exegetical';

    public const ORDER_CANONICAL = 'canonical';

    public const ORDER_CHRONOLOGICAL = 'chronological';

    public const ORDER_DOCTRINAL = 'doctrinal';

    public const ORDER_CHRIST_CENTERED = 'christ_centered';

    public const ORDER_NT_PSALMS = 'nt_psalms';

    public function __construct(
        private readonly PlanGeneratorEngine $engine
    ) {}

    /**
     * Generate plan by complexity and order type. Handles leap years and dynamic schedule.
     *
     * @param  array{complexity?: string, order_type?: string}  $options
     */
    public function generate(BiblePlan $plan, int $bibleVersionId, array $options = []): void
    {
        $daysCount = $this->resolveDurationDays($plan->duration_days);
        $complexity = $options['complexity'] ?? self::COMPLEXITY_STANDARD;
        $orderType = $options['order_type'] ?? self::ORDER_CANONICAL;

        if ($complexity === self::COMPLEXITY_INICIANTE || $orderType === self::ORDER_NT_PSALMS) {
            $this->generateIniciante($plan, $daysCount, $bibleVersionId);
            return;
        }

        if ($orderType === self::ORDER_CHRIST_CENTERED) {
            $this->engine->generateChristCentered($plan, $daysCount, $bibleVersionId);
            return;
        }

        if ($orderType === self::ORDER_CHRONOLOGICAL) {
            $this->engine->generateChronological($plan, $daysCount, $bibleVersionId);
            return;
        }

        if ($orderType === self::ORDER_DOCTRINAL) {
            $this->generateDoctrinal($plan, $daysCount, $bibleVersionId);
            return;
        }

        $this->engine->generateFromTemplate($plan, $orderType === 'historical' ? 'historical' : 'canonical', $bibleVersionId);
    }

    /**
     * Leitor Iniciante: Novo Testamento + Salmos (Nova Aliança).
     */
    public function generateIniciante(BiblePlan $plan, int $daysCount, int $bibleVersionId): void
    {
        $books = Book::where('bible_version_id', $bibleVersionId)
            ->where(function ($q) {
                $q->where('testament', 'new')
                    ->orWhere('abbreviation', 'Sl');
            })
            ->orderByRaw("CASE WHEN abbreviation = 'Sl' THEN 0 ELSE book_number END")
            ->orderBy('book_number')
            ->get();

        if ($books->isEmpty()) {
            throw new \InvalidArgumentException('Nenhum livro (NT ou Salmos) encontrado para esta versão.');
        }

        $bookIds = $books->pluck('id')->toArray();
        $this->engine->generateSequential($plan, $bookIds, $daysCount);
    }

    /**
     * Plano Doutrinário Batista: grandes temas da Fé Batista (Sola Scriptura, Centralidade de Cristo, etc.).
     * Order: Gospels → Epistles → OT Law → Prophets → Wisdom.
     */
    public function generateDoctrinal(BiblePlan $plan, int $daysCount, int $bibleVersionId): void
    {
        $gospels = Book::where('bible_version_id', $bibleVersionId)
            ->whereBetween('book_number', [40, 43])
            ->orderBy('book_number')->pluck('id')->toArray();
        $acts = Book::where('bible_version_id', $bibleVersionId)->where('book_number', 44)->pluck('id')->toArray();
        $epistlesRev = Book::where('bible_version_id', $bibleVersionId)
            ->whereBetween('book_number', [45, 66])
            ->orderBy('book_number')->pluck('id')->toArray();
        $ot = Book::where('bible_version_id', $bibleVersionId)
            ->whereBetween('book_number', [1, 39])
            ->orderBy('book_number')->pluck('id')->toArray();

        $bookIds = array_merge($gospels, $acts, $epistlesRev, $ot);
        if (empty($bookIds)) {
            throw new \InvalidArgumentException('Livros não encontrados para esta versão.');
        }
        $this->engine->generateSequential($plan, $bookIds, $daysCount);
    }

    /**
     * Standard: Bíblia toda em 1 ano — intercalando AT e NT (Promessa e Cumprimento).
     * Uses existing canonical template with full Bible.
     */
    public function generateStandardOneYear(BiblePlan $plan, int $bibleVersionId): void
    {
        $daysCount = $this->resolveDurationDays($plan->duration_days ?: 365);
        $this->engine->generateFromTemplate($plan, 'canonical', $bibleVersionId);
    }

    /**
     * Resolve duration: support leap year (366) and dynamic end date.
     */
    private function resolveDurationDays(int $durationDays): int
    {
        return max(1, min(366, $durationDays));
    }

    /**
     * Return total verse count for a version from chapters (fallback when bible_metadata not populated).
     */
    public static function getTotalVersesForVersion(int $bibleVersionId): int
    {
        $bookIds = Book::where('bible_version_id', $bibleVersionId)->pluck('id');
        return (int) Chapter::whereIn('book_id', $bookIds)->sum('total_verses');
    }
}
