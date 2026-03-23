<?php

namespace Modules\Bible\App\Services;

use Illuminate\Support\Facades\DB;
use Modules\Bible\App\Models\BibleMetadata;
use Modules\Bible\App\Models\BiblePlan;
use Modules\Bible\App\Models\BiblePlanContent;
use Modules\Bible\App\Models\BiblePlanDay;
use Modules\Bible\App\Models\Book;

class PlanGeneratorEngine
{
    /**
     * Core distribution logic: Splits a list of ordered chapters across X days based on VERSE count.
     */
    private function distributeVerses(BiblePlan $plan, array $allChapters, int $daysCount)
    {
        // 1. Calculate Total Verses Scope
        $totalVersesScope = 0;
        foreach ($allChapters as $ch) {
            // Ensure we have a valid count, fallback to 30 if missing/zero to prevent crashes
            $count = isset($ch['total_verses']) && $ch['total_verses'] > 0 ? $ch['total_verses'] : 25;
            $totalVersesScope += $count;
        }

        if ($totalVersesScope === 0) {
            return;
        }

        $versesPerDay = $totalVersesScope / $daysCount;
        $maxVersesFromChapterPerDay = (int) ceil(2 * $versesPerDay);

        // Cursor state
        $cursor = [
            'chapter_idx' => 0, // Index in $allChapters array
            'verse_num' => 1    // 1-based verse number in current chapter
        ];

        $versesAssignedTotal = 0;
        $totalChapters = count($allChapters);

        DB::transaction(function () use ($plan, $daysCount, $allChapters, $totalChapters, $versesPerDay, $maxVersesFromChapterPerDay, &$cursor, &$versesAssignedTotal) {
            // Clear existing days
            $plan->days()->each(function ($day) {
                $day->contents()->delete();
                $day->delete();
            });

            for ($day = 1; $day <= $daysCount; $day++) {
                if ($cursor['chapter_idx'] >= $totalChapters) {
                    break; // Done early
                }

                // Calculate quota for today (Accumulator method for precision)
                $targetCumulative = round($day * $versesPerDay);
                $quota = max(0, $targetCumulative - $versesAssignedTotal);

                // If last day, force take everything remaining
                if ($day == $daysCount) {
                    $quota = 999999;
                }

                if ($quota <= 0) {
                    // Create empty/catchup day
                    BiblePlanDay::create(['plan_id' => $plan->id, 'day_number' => $day, 'title' => 'Dia '.$day]);
                    continue;
                }

                $todaysReadings = [];

                // Fill the quota
                while ($quota > 0 && $cursor['chapter_idx'] < $totalChapters) {
                    $currentChapter = $allChapters[$cursor['chapter_idx']];
                    $totalVersesInChapter = isset($currentChapter['total_verses']) && $currentChapter['total_verses'] > 0 ? $currentChapter['total_verses'] : 25;

                    $versesRemainingInChapter = $totalVersesInChapter - $cursor['verse_num'] + 1;

                    if ($versesRemainingInChapter <= $quota) {
                        // Fragment giant chapters: cap at 2x daily average so we don't assign e.g. Psalm 119 in one day
                        if ($totalVersesInChapter > $maxVersesFromChapterPerDay && $versesRemainingInChapter > $maxVersesFromChapterPerDay) {
                            $take = $maxVersesFromChapterPerDay;
                            $endVerse = $cursor['verse_num'] + $take - 1;
                            $todaysReadings[] = [
                                'book_id' => $currentChapter['book_id'],
                                'chapter_num' => $currentChapter['chapter_number'],
                                'start_verse' => $cursor['verse_num'],
                                'end_verse' => $endVerse,
                            ];
                            $quota -= $take;
                            $versesAssignedTotal += $take;
                            $cursor['verse_num'] = $endVerse + 1;
                        } else {
                            // Consume rest of chapter
                            $todaysReadings[] = [
                                'book_id' => $currentChapter['book_id'],
                                'chapter_num' => $currentChapter['chapter_number'],
                                'start_verse' => $cursor['verse_num'],
                                'end_verse' => $totalVersesInChapter,
                            ];
                            $quota -= $versesRemainingInChapter;
                            $versesAssignedTotal += $versesRemainingInChapter;
                            $cursor['chapter_idx']++;
                            $cursor['verse_num'] = 1;
                        }
                    } else {
                        // Consume partial chapter (quota runs out here); cap for giant chapters
                        $effectiveQuota = $quota;
                        if ($totalVersesInChapter > $maxVersesFromChapterPerDay) {
                            $effectiveQuota = min($quota, $maxVersesFromChapterPerDay);
                        }
                        $endVerse = $cursor['verse_num'] + $effectiveQuota - 1;

                        $todaysReadings[] = [
                            'book_id' => $currentChapter['book_id'],
                            'chapter_num' => $currentChapter['chapter_number'],
                            'start_verse' => $cursor['verse_num'],
                            'end_verse' => $endVerse,
                        ];

                        $versesAssignedTotal += $effectiveQuota;
                        $quota -= $effectiveQuota;

                        // Move cursor within same chapter
                        $cursor['verse_num'] = $endVerse + 1;
                        if ($quota <= 0) {
                            $quota = 0;
                        }
                    }
                }

                if (empty($todaysReadings)) {
                    continue;
                }

                // Create Day
                $planDay = BiblePlanDay::create([
                    'plan_id' => $plan->id,
                    'day_number' => $day,
                    'title' => 'Dia '.$day,
                ]);

                // Group Readings by Book to create contiguous ranges
                // Example: Gen 1:1-31, Gen 2:1-25 -> Gen 1:1 - 2:25
                $grouped = [];
                $lastReading = null;

                foreach ($todaysReadings as $reading) {
                    if ($lastReading && $lastReading['book_id'] === $reading['book_id']) {
                        // Check adjacency: Does this reading start where last ended + 1?
                        // Actually, logic guarantees sequence. We just check if it's the NEXT chapter?
                        // Or same chapter (impossible via loop logic unless we loop back, but we strictly advance).
                        // Yes, if same book, we can merge.

                        // Is it contiguous chapters?
                        // Reading A: Chap 1, End 31. Reading B: Chap 2, Start 1.
                        // Yes.
                        $lastReading['chapter_end'] = $reading['chapter_num'];
                        $lastReading['verse_end'] = $reading['end_verse'];
                        // Verse start remains from first.
                    } else {
                        if ($lastReading) {
                            $grouped[] = $lastReading;
                        }
                        $lastReading = [
                            'book_id' => $reading['book_id'],
                            'chapter_start' => $reading['chapter_num'],
                            'chapter_end' => $reading['chapter_num'],
                            'verse_start' => $reading['start_verse'],
                            'verse_end' => $reading['end_verse'],
                        ];
                    }
                }
                if ($lastReading) {
                    $grouped[] = $lastReading;
                }

                // Save to DB
                $orderIndex = 0;
                foreach ($grouped as $g) {
                    BiblePlanContent::create([
                        'plan_day_id' => $planDay->id,
                        'order_index' => $orderIndex++,
                        'type' => 'scripture',
                        'book_id' => $g['book_id'],
                        'chapter_start' => $g['chapter_start'],
                        'chapter_end' => $g['chapter_end'],
                        'verse_start' => $g['verse_start'],
                        'verse_end' => $g['verse_end'],
                    ]);
                }
            }
        });
    }

    public function generateSequential(BiblePlan $plan, array $bookIds, int $daysCount)
    {
        $books = Book::with('chapters')->whereIn('id', $bookIds)->orderBy('order')->get();
        $versionId = $books->first()?->bible_version_id;
        $metadataMap = $this->getVerseCountMapForVersion($versionId, $bookIds);
        $allChapters = [];

        foreach ($books as $book) {
            foreach ($book->chapters as $chapter) {
                $key = $book->id.'_'.$chapter->chapter_number;
                $totalVerses = $metadataMap[$key] ?? $chapter->total_verses;
                $totalVerses = $totalVerses > 0 ? (int) $totalVerses : 25;
                $allChapters[] = [
                    'book_id' => $book->id,
                    'book_name' => $book->name,
                    'chapter_number' => $chapter->chapter_number,
                    'chapter_id' => $chapter->id,
                    'total_verses' => $totalVerses,
                ];
            }
        }

        $this->distributeVerses($plan, $allChapters, $daysCount);
    }

    /**
     * Version-dependent verse counts from bible_metadata when populated (i18n); otherwise empty (caller uses Chapter fallback).
     *
     * @param  array<int>  $bookIds
     * @return array<string, int>  key = "bookId_chapterNumber", value = verse_count
     */
    private function getVerseCountMapForVersion(?int $versionId, array $bookIds): array
    {
        if (! $versionId || ! class_exists(BibleMetadata::class)) {
            return [];
        }
        $rows = BibleMetadata::query()
            ->where('bible_version_id', $versionId)
            ->whereIn('book_id', $bookIds)
            ->get(['book_id', 'chapter_number', 'verse_count']);
        $map = [];
        foreach ($rows as $row) {
            $map[$row->book_id.'_'.$row->chapter_number] = (int) $row->verse_count;
        }
        return $map;
    }

    /**
     * Distribute verses into a range of days (for catch-up: replace days from startDayNumber).
     */
    public function distributeVersesForRange(BiblePlan $plan, array $allChapters, int $daysCount, int $startDayNumber = 1): void
    {
        $totalVersesScope = 0;
        foreach ($allChapters as $ch) {
            $count = isset($ch['total_verses']) && $ch['total_verses'] > 0 ? $ch['total_verses'] : 25;
            $totalVersesScope += $count;
        }
        if ($totalVersesScope === 0) {
            return;
        }

        $versesPerDay = $totalVersesScope / $daysCount;
        $maxVersesFromChapterPerDay = (int) ceil(2 * $versesPerDay);
        $cursor = ['chapter_idx' => 0, 'verse_num' => 1];
        $versesAssignedTotal = 0;
        $totalChapters = count($allChapters);

        DB::transaction(function () use ($plan, $daysCount, $startDayNumber, $allChapters, $totalChapters, $versesPerDay, $maxVersesFromChapterPerDay, &$cursor, &$versesAssignedTotal) {
            $plan->days()->where('day_number', '>=', $startDayNumber)->each(function ($day) {
                $day->contents()->delete();
                $day->delete();
            });

            for ($offset = 0; $offset < $daysCount; $offset++) {
                $dayNumber = $startDayNumber + $offset;
                if ($cursor['chapter_idx'] >= $totalChapters) {
                    break;
                }
                $targetCumulative = round(($offset + 1) * $versesPerDay);
                $quota = max(0, (int) ($targetCumulative - $versesAssignedTotal));
                if ($offset === $daysCount - 1) {
                    $quota = 999999;
                }
                if ($quota <= 0) {
                    BiblePlanDay::create(['plan_id' => $plan->id, 'day_number' => $dayNumber, 'title' => 'Dia '.$dayNumber]);
                    continue;
                }
                $todaysReadings = [];
                while ($quota > 0 && $cursor['chapter_idx'] < $totalChapters) {
                    $currentChapter = $allChapters[$cursor['chapter_idx']];
                    $totalVersesInChapter = isset($currentChapter['total_verses']) && $currentChapter['total_verses'] > 0 ? (int) $currentChapter['total_verses'] : 25;
                    $versesRemainingInChapter = $totalVersesInChapter - $cursor['verse_num'] + 1;
                    if ($versesRemainingInChapter <= $quota) {
                        if ($totalVersesInChapter > $maxVersesFromChapterPerDay && $versesRemainingInChapter > $maxVersesFromChapterPerDay) {
                            $take = $maxVersesFromChapterPerDay;
                            $endVerse = $cursor['verse_num'] + $take - 1;
                            $todaysReadings[] = [
                                'book_id' => $currentChapter['book_id'],
                                'chapter_num' => $currentChapter['chapter_number'],
                                'start_verse' => $cursor['verse_num'],
                                'end_verse' => $endVerse,
                            ];
                            $quota -= $take;
                            $versesAssignedTotal += $take;
                            $cursor['verse_num'] = $endVerse + 1;
                        } else {
                            $todaysReadings[] = [
                                'book_id' => $currentChapter['book_id'],
                                'chapter_num' => $currentChapter['chapter_number'],
                                'start_verse' => $cursor['verse_num'],
                                'end_verse' => $totalVersesInChapter,
                            ];
                            $quota -= $versesRemainingInChapter;
                            $versesAssignedTotal += $versesRemainingInChapter;
                            $cursor['chapter_idx']++;
                            $cursor['verse_num'] = 1;
                        }
                    } else {
                        $effectiveQuota = $quota;
                        if ($totalVersesInChapter > $maxVersesFromChapterPerDay) {
                            $effectiveQuota = min($quota, $maxVersesFromChapterPerDay);
                        }
                        $endVerse = $cursor['verse_num'] + $effectiveQuota - 1;
                        $todaysReadings[] = [
                            'book_id' => $currentChapter['book_id'],
                            'chapter_num' => $currentChapter['chapter_number'],
                            'start_verse' => $cursor['verse_num'],
                            'end_verse' => $endVerse,
                        ];
                        $versesAssignedTotal += $effectiveQuota;
                        $cursor['verse_num'] = $endVerse + 1;
                        $quota -= $effectiveQuota;
                        if ($quota <= 0) {
                            $quota = 0;
                        }
                    }
                }
                if (empty($todaysReadings)) {
                    continue;
                }
                $planDay = BiblePlanDay::create([
                    'plan_id' => $plan->id,
                    'day_number' => $dayNumber,
                    'title' => 'Dia '.$dayNumber,
                ]);
                $grouped = $this->groupReadingsContiguous($todaysReadings);
                $orderIndex = 0;
                foreach ($grouped as $g) {
                    BiblePlanContent::create([
                        'plan_day_id' => $planDay->id,
                        'order_index' => $orderIndex++,
                        'type' => 'scripture',
                        'book_id' => $g['book_id'],
                        'chapter_start' => $g['chapter_start'],
                        'chapter_end' => $g['chapter_end'],
                        'verse_start' => $g['verse_start'],
                        'verse_end' => $g['verse_end'],
                    ]);
                }
            }
        });
    }

    private function groupReadingsContiguous(array $todaysReadings): array
    {
        $grouped = [];
        $lastReading = null;
        foreach ($todaysReadings as $reading) {
            if ($lastReading && $lastReading['book_id'] === $reading['book_id']) {
                $lastReading['chapter_end'] = $reading['chapter_num'];
                $lastReading['verse_end'] = $reading['end_verse'];
            } else {
                if ($lastReading) {
                    $grouped[] = $lastReading;
                }
                $lastReading = [
                    'book_id' => $reading['book_id'],
                    'chapter_start' => $reading['chapter_num'],
                    'chapter_end' => $reading['chapter_num'],
                    'verse_start' => $reading['start_verse'],
                    'verse_end' => $reading['end_verse'],
                ];
            }
        }
        if ($lastReading) {
            $grouped[] = $lastReading;
        }
        return $grouped;
    }

    public function generateChronological(BiblePlan $plan, int $daysCount, $versionId = null)
    {
        $query = Book::orderBy('order');
        if ($versionId) {
            $query->where('bible_version_id', $versionId);
        }
        $this->generateSequential($plan, $query->pluck('id')->toArray(), $daysCount);
    }

    public function generateChristCentered(BiblePlan $plan, int $daysCount, int $versionId)
    {
        // Strategy: Gospels -> Acts -> Epistles/Rev -> OT
        // Using book_number for safety (Protestant Standard)
        // Gospels: 40-43, Acts: 44, Epistles+Rev: 45-66, OT: 1-39

        $gospels = Book::where('bible_version_id', $versionId)
            ->whereBetween('book_number', [40, 43])
            ->orderBy('book_number')
            ->get();

        $acts = Book::where('bible_version_id', $versionId)
            ->where('book_number', 44)
            ->get();

        $epistlesAndRev = Book::where('bible_version_id', $versionId)
            ->whereBetween('book_number', [45, 66])
            ->orderBy('book_number')
            ->get();

        $ot = Book::where('bible_version_id', $versionId)
            ->whereBetween('book_number', [1, 39])
            ->orderBy('book_number')
            ->get();

        // Merge in the requested order
        $allBooks = $gospels->merge($acts)->merge($epistlesAndRev)->merge($ot);

        if ($allBooks->isEmpty()) {
            throw new \Exception('Livros não encontrados para esta versão.');
        }

        $allChapters = [];
        $allBooks->load('chapters');

        foreach ($allBooks as $book) {
            foreach ($book->chapters as $chapter) {
                $allChapters[] = [
                    'book_id' => $book->id,
                    'book_name' => $book->name,
                    'chapter_number' => $chapter->chapter_number,
                    'chapter_id' => $chapter->id,
                    'total_verses' => $chapter->total_verses,
                ];
            }
        }

        $this->distributeVerses($plan, $allChapters, $daysCount);
    }

    public function generateFromTemplate(BiblePlan $plan, string $templateType, int $versionId)
    {
        // Custom logic for Christ Centered dynamic generation
        if ($templateType === 'christ_centered') {
            $this->generateChristCentered($plan, $plan->duration_days, $versionId);
            return;
        }

        switch ($templateType) {
            case 'canonical':
                $schedule = BiblePlanTemplates::getCanonical();
                break;
            case 'historical':
                $schedule = BiblePlanTemplates::getHistorical();
                break;
            default:
                throw new \Exception('Template desconhecido.');
        }

        if (empty($schedule)) {
            throw new \Exception('Template vazio.');
        }

        // Flatten Template into Chapter List
        $booksMap = Book::where('bible_version_id', $versionId)->pluck('id', 'abbreviation')->toArray();
        // Optimization: Get chapter counts in one go
        $chaptersData = \Modules\Bible\App\Models\Chapter::whereIn('book_id', array_values($booksMap))
            ->get()
            ->groupBy('book_id');

        // Load Maps for mapping abbreviations
        $abbrMap = [
            'Gn' => 'Gn', 'Ex' => 'Êx', 'Êx' => 'Êx', 'Lv' => 'Lv', 'Nm' => 'Nm', 'Dt' => 'Dt',
            'Js' => 'Js', 'Jz' => 'Jz', 'rt' => 'Rt', 'Rt' => 'Rt',
            '1Sm' => '1Sm', '2Sm' => '2Sm', '1Rs' => '1Rs', '2Rs' => '2Rs',
            '1cr' => '1Cr', '2cr' => '2Cr', '1Cr' => '1Cr', '2Cr' => '2Cr',
            'Ed' => 'Ed', 'Ne' => 'Ne', 'Et' => 'Et',
            'JOb' => 'Jó', 'Job' => 'Jó', 'Jó' => 'Jó', 'Sl' => 'Sl', 'Pv' => 'Pv', 'Ec' => 'Ec', 'Ct' => 'Ct',
            'Is' => 'Is', 'Jr' => 'Jr', 'Lm' => 'Lm', 'Ez' => 'Ez', 'Dn' => 'Dn',
            'Os' => 'Os', 'Jl' => 'Jl', 'Am' => 'Am', 'Ob' => 'Ob', 'Jn' => 'Jn',
            'Mq' => 'Mq', 'Na' => 'Na', 'Hc' => 'Hc', 'Sf' => 'Sf', 'Ag' => 'Ag',
            'Zc' => 'Zc', 'Ml' => 'Ml',
            'Mt' => 'Mt', 'Mc' => 'Mc', 'Lc' => 'Lc', 'Jo' => 'Jo', 'At' => 'At',
            'Rm' => 'Rm', '1Co' => '1Co', '2Co' => '2Co', 'Gl' => 'Gl', 'Ef' => 'Ef',
            'Fp' => 'Fp', 'Cl' => 'Cl', '1Ts' => '1Ts', '2Ts' => '2Ts', '1Tm' => '1Tm',
            '2Tm' => '2Tm', 'Tt' => 'Tt', 'Fm' => 'Fm', 'Hb' => 'Hb', 'Tg' => 'Tg',
            '1Pe' => '1Pe', '2Pe' => '2Pe', '1Jo' => '1Jo', '2Jo' => '2Jo', '3Jo' => '3Jo',
            'Jd' => 'Jd', 'Ap' => 'Ap',
        ];

        $flatChapters = [];

        // Iterate through Template Order
        foreach ($schedule as $dayNum => $readings) {
            foreach ($readings as $reading) {
                // Find Book ID
                $abbr = $reading['book'];
                $bookId = $booksMap[$abbr] ?? ($booksMap[$abbrMap[$abbr] ?? ''] ?? null);

                if (! $bookId) {
                    continue;
                }

                $bookChapters = $chaptersData[$bookId] ?? collect();

                // Expand range into individual chapters
                for ($ch = $reading['start']; $ch <= $reading['end']; $ch++) {
                    // Find actual chapter data for verse count
                    $chapterModel = $bookChapters->where('chapter_number', $ch)->first();
                    $verseCount = $chapterModel ? $chapterModel->total_verses : 25; // Fallback

                    $flatChapters[] = [
                        'book_id' => $bookId,
                        'chapter_number' => $ch,
                        'total_verses' => $verseCount
                    ];
                }
            }
        }

        if (empty($flatChapters)) {
            throw new \Exception('Não foi possível processar os capítulos do template.');
        }

        $this->distributeVerses($plan, $flatChapters, $plan->duration_days);
    }
}
