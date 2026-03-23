<?php

namespace Modules\Bible\Services;

use Illuminate\Support\Facades\DB;
use Modules\Bible\App\Models\BiblePlan;
use Modules\Bible\App\Models\BiblePlanDay;
use Modules\Bible\App\Models\BiblePlanReading;
use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Models\Chapter;

class PlanGeneratorEngine
{
    // Standard Chronological Order of Books (by Book Number)
    // Based on typical "Read the Bible in Chronological Order" plans.
    // 1=Gen, 18=Job, 2=Ex, ...
    private const CHRONOLOGICAL_ORDER = [
        1,  // Genesis
        18, // Job
        2,  // Exodus
        3,  // Leviticus
        4,  // Numbers
        5,  // Deuteronomy
        6,  // Joshua
        7,  // Judges
        8,  // Ruth
        9,  // 1 Samuel
        10, // 2 Samuel
        13, // 1 Chronicles
        19, // Psalms
        22, // Song of Solomon
        20, // Proverbs
        21, // Ecclesiastes
        11, // 1 Kings
        12, // 2 Kings
        14, // 2 Chronicles
        23, // Isaiah
        24, // Jeremiah
        25, // Lamentations
        29, // Joel
        30, // Amos
        31, // Obadiah
        32, // Jonah
        33, // Micah
        34, // Nahum
        35, // Habakkuk
        36, // Zephaniah
        37, // Haggai
        38, // Zechariah
        39, // Malachi
        26, // Ezekiel
        27, // Daniel
        28, // Hosea
        15, // Ezra
        16, // Nehemiah
        17, // Esther
        40, // Matthew
        41, // Mark
        42, // Luke
        43, // John
        44, // Acts
        45, // Romans
        46, // 1 Corinthians
        47, // 2 Corinthians
        48, // Galatians
        49, // Ephesians
        50, // Philippians
        51, // Colossians
        52, // 1 Thessalonians
        53, // 2 Thessalonians
        54, // 1 Timothy
        55, // 2 Timothy
        56, // Titus
        57, // Philemon
        58, // Hebrews
        59, // James
        60, // 1 Peter
        61, // 2 Peter
        62, // 1 John
        63, // 2 John
        64, // 3 John
        65, // Jude
        66, // Revelation
    ];

    /**
     * Generates the plan using a "Load Distribution" algorithm.
     *
     * @param BiblePlan $plan The plan model to attach days to.
     * @param string $type 'chronological', 'sequential', 'manual'
     * @param int $totalDays Number of days (e.g. 365)
     * @param array $customBookIds Optional array of book IDs for custom/sequential plans.
     */
    public function generate(BiblePlan $plan, string $type = 'sequential', int $totalDays = 365, array $customBookIds = [])
    {
        // 1. Fetch Scope
        $booksQuery = Book::query();

        if ($type === 'chronological') {
             // For chronological, we usually assume the whole Bible unless specified otherwise.
             // If the user wants a chronological NT, we'd filter by NT + sort by chrono.
             // For now, assuming Full Bible if chronological, or we could filter existing books.
             // Let's load all and filter/sort in memory for complex ordering.
             $books = Book::with('chapters')->get();
        } elseif (!empty($customBookIds)) {
            $books = Book::whereIn('id', $customBookIds)->with('chapters')->get();
        } else {
            // Default fallback
            $books = Book::with('chapters')->get();
        }

        // 2. Flatten to Chapters and Sort
        $allChapters = collect();

        // Sort books first
        $sortedBooks = $books->sort(function($a, $b) use ($type) {
            if ($type === 'chronological') {
                $posA = array_search($a->book_number, self::CHRONOLOGICAL_ORDER);
                $posB = array_search($b->book_number, self::CHRONOLOGICAL_ORDER);
                // If not found in chrono list (e.g. Apocrypha), push to end
                $posA = $posA === false ? 999 : $posA;
                $posB = $posB === false ? 999 : $posB;
                return $posA <=> $posB;
            } else {
                return $a->book_number <=> $b->book_number; // Canonical/Sequential
            }
        });

        foreach ($sortedBooks as $book) {
            foreach ($book->chapters as $chapter) {
                // Attach book info to chapter for easy access later
                $chapter->book_ref = $book;
                $allChapters->push($chapter);
            }
        }

        $totalChapters = $allChapters->count();
        if ($totalChapters === 0) return false;

        // 3. Distribution Algorithm (Accumulator)
        $dailyAverage = $totalChapters / $totalDays;
        $chaptersAssigned = 0;

        DB::beginTransaction();
        try {
            // Clear existing
            $plan->days()->delete();

            $chapterIndex = 0;

            for ($day = 1; $day <= $totalDays; $day++) {
                // Calculate target for this day
                // Example: 1189 caps / 365 days = 3.257
                // Day 1: ceil(3.257 * 1) = 4. Assigned 0. Need 4.
                // Day 2: ceil(3.257 * 2) = 7. Assigned 4. Need 3.
                // Day 3: ceil(3.257 * 3) = 10. Assigned 7. Need 3.

                // We use ceil to slightly front-load or ensure completion,
                // but strictly speaking, Rounding Half Up or simple Ceil on the cumulative is best to avoid "0" days.
                // Let's use Round to nearest integer for the cumulative target to smooth it out.
                // Day 365: round(3.257 * 365) = 1189. Perfect.

                $targetCumulative = round($dailyAverage * $day);

                // Ensure last day takes ALL remainder no matter what rounding did
                if ($day == $totalDays) {
                    $targetCumulative = $totalChapters;
                }

                $quotaForToday = max(0, $targetCumulative - $chaptersAssigned);

                if ($quotaForToday == 0) {
                    // It's possible if total chapters < total days.
                    // Create an empty day or skip?
                    // Better to create a "Rest/Catchup" day.
                    BiblePlanDay::create([
                        'plan_id' => $plan->id,
                        'day_number' => $day,
                        'title' => "Dia $day - Descanso / Leitura em dia",
                    ]);
                    continue;
                }

                // Slice chapters
                $daysChapters = $allChapters->slice($chapterIndex, $quotaForToday);
                $chapterIndex += $quotaForToday;
                $chaptersAssigned += $quotaForToday;

                // Create Day
                $planDay = BiblePlanDay::create([
                    'plan_id' => $plan->id,
                    'day_number' => $day,
                    'title' => "Dia $day",
                ]);

                // Batch Readings
                // We need to group sequential chapters of the same book to avoid "Gen 1, Gen 2, Gen 3" as separate lines.
                // We want "Gen 1-3".

                $groupedReadings = [];
                $currentGroup = null;

                foreach ($daysChapters as $chapter) {
                    $bookId = $chapter->book_ref->id;
                    $chapNum = $chapter->chapter_number;

                    if ($currentGroup && $currentGroup['book_id'] === $bookId && $chapNum == $currentGroup['end_chapter'] + 1) {
                        // Extend current group
                        $currentGroup['end_chapter'] = $chapNum;
                    } else {
                        // Save previous group if exists
                        if ($currentGroup) {
                            $groupedReadings[] = $currentGroup;
                        }
                        // Start new group
                        $currentGroup = [
                            'book_id' => $bookId,
                            'book_name' => $chapter->book_ref->name,
                            'start_chapter' => $chapNum,
                            'end_chapter' => $chapNum
                        ];
                    }
                }
                // Save last group
                if ($currentGroup) {
                    $groupedReadings[] = $currentGroup;
                }

                foreach ($groupedReadings as $reading) {
                    BiblePlanReading::create([
                        'plan_day_id' => $planDay->id,
                        'book_id' => $reading['book_id'],
                        'start_chapter' => $reading['start_chapter'],
                        'end_chapter' => $reading['end_chapter'],
                        'description_cache' => $reading['book_name'] . ' ' . $reading['start_chapter'] .
                                              ($reading['end_chapter'] > $reading['start_chapter'] ? '-' . $reading['end_chapter'] : ''),
                        'type' => 'scripture'
                    ]);
                }
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
