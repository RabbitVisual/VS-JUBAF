<?php

namespace Modules\Bible\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Bible\App\Models\BiblePlanDay;
use Modules\Bible\App\Models\BiblePlanSubscription;
use Modules\Bible\App\Models\BibleUserProgress;
use Modules\Bible\App\Models\UserReadingLog;
use Modules\Bible\App\Services\BadgeService;

class PlanReaderController extends Controller
{
    public function read($subscriptionId, $dayNumber)
    {
        $subscription = BiblePlanSubscription::with('plan')
            ->where('user_id', Auth::id())
            ->where('id', $subscriptionId)
            ->firstOrFail();

        $day = BiblePlanDay::with(['contents.book']) // eager loading contents relationship
            ->where('plan_id', $subscription->plan_id)
            ->where('day_number', $dayNumber)
            ->firstOrFail();

        // Determine Target Version
        $versionAbbr = request('version');
        if ($versionAbbr) {
            $targetVersion = \Modules\Bible\App\Models\BibleVersion::whereRaw('LOWER(abbreviation) = ?', [strtolower($versionAbbr)])->first();
        }

        // Fallbacks: Settings global default -> is_default -> first active
        if (! isset($targetVersion) || ! $targetVersion) {
            $globalAbbr = \App\Models\Settings::get('default_bible_version_abbreviation', '');
            if ($globalAbbr !== '') {
                $targetVersion = \Modules\Bible\App\Models\BibleVersion::where('abbreviation', $globalAbbr)
                    ->where('is_active', true)->first();
            }
            if (! isset($targetVersion) || ! $targetVersion) {
                $targetVersion = \Modules\Bible\App\Models\BibleVersion::where('is_default', true)->first()
                    ?? \Modules\Bible\App\Models\BibleVersion::where('is_active', true)->first();
            }
        }

        // Hydrate contents with actual scripture text
        foreach ($day->contents as $content) {
            if ($content->type === 'scripture') {
                // Determine which book ID to search in the TARGET version
                $actualTargetBookId = null;
                $lookupBookNumber = null;
                $lookupBookName = null;

                // Priority 1: Use direct relationship
                if ($content->book) {
                    $lookupBookNumber = $content->book->book_number;
                    $lookupBookName = $content->book->name;
                } else {
                    // Priority 2: Try to find original book manually if ID exists but relation failed
                    $originalBookFallback = \Modules\Bible\App\Models\Book::find($content->book_id);
                    if ($originalBookFallback) {
                        $lookupBookNumber = $originalBookFallback->book_number;
                        $lookupBookName = $originalBookFallback->name;
                    }
                }

                if ($targetVersion && $lookupBookNumber) {
                    // Try to map by number (most reliable)
                    $smartBook = \Modules\Bible\App\Models\Book::where('bible_version_id', $targetVersion->id)
                        ->where('book_number', $lookupBookNumber)
                        ->first();

                    if ($smartBook) {
                        $actualTargetBookId = $smartBook->id;
                        $content->target_book_name = $smartBook->name;
                    } elseif ($lookupBookName) {
                        // Fallback: search by name in target version
                        $smartBookByName = \Modules\Bible\App\Models\Book::where('bible_version_id', $targetVersion->id)
                            ->where('name', $lookupBookName)
                            ->first();
                        if ($smartBookByName) {
                            $actualTargetBookId = $smartBookByName->id;
                            $content->target_book_name = $smartBookByName->name;
                            $content->target_book_id = $smartBookByName->id;
                        }
                    }
                }

                // If we found a valid book in the target version, fetch verses
                if ($actualTargetBookId) {
                    $chapterStart = (int) $content->chapter_start;
                    $chapterEnd = (int) ($content->chapter_end ?: $content->chapter_start);
                    $verseStart = $content->verse_start;
                    $verseEnd = $content->verse_end;

                    $query = \Modules\Bible\App\Models\Verse::select('verses.*')
                        ->join('chapters', 'verses.chapter_id', '=', 'chapters.id')
                        ->where('chapters.book_id', $actualTargetBookId)
                        ->orderBy('chapters.chapter_number')
                        ->orderBy('verses.verse_number')
                        ->with('chapter');

                    // Advanced Verse Filtering for Multi-Chapter Support
                    if ($chapterStart == $chapterEnd) {
                        // Single Chapter: Simple filter
                        $query->where('chapters.chapter_number', $chapterStart);
                        if ($verseStart) $query->where('verses.verse_number', '>=', $verseStart);
                        if ($verseEnd) $query->where('verses.verse_number', '<=', $verseEnd);
                    } else {
                        // Multi Chapter: Complex filter
                        $query->where(function ($q) use ($chapterStart, $chapterEnd, $verseStart, $verseEnd) {
                            // 1. First Chapter (from start verse to end)
                            $q->where(function ($sub) use ($chapterStart, $verseStart) {
                                $sub->where('chapters.chapter_number', $chapterStart);
                                if ($verseStart) $sub->where('verses.verse_number', '>=', $verseStart);
                            });

                            // 2. Middle Chapters (all verses)
                            if ($chapterEnd > $chapterStart + 1) {
                                $q->orWhereBetween('chapters.chapter_number', [$chapterStart + 1, $chapterEnd - 1]);
                            }

                            // 3. Last Chapter (from 1 to end verse)
                            $q->orWhere(function ($sub) use ($chapterEnd, $verseEnd) {
                                $sub->where('chapters.chapter_number', $chapterEnd);
                                if ($verseEnd) $sub->where('verses.verse_number', '<=', $verseEnd);
                            });
                        });
                    }

                    $content->verses = $query->get()->groupBy(fn ($verse) => $verse->chapter->chapter_number);
                } else {
                    $content->verses = collect();
                }
            }
        }

        // Check if completed
        $isCompleted = BibleUserProgress::where('subscription_id', $subscription->id)
            ->where('plan_day_id', $day->id)
            ->exists();

        // Enforce Back Tracking Rule
        $isLocked = false;
        if ($isCompleted && ! $subscription->plan->allow_back_tracking) {
            $isLocked = true;
        }

        // Previous/Next logic
        $prevDay = $dayNumber > 1 ? $dayNumber - 1 : null;
        $totalDays = $subscription->plan->days()->count();
        $nextDay = $dayNumber < $totalDays ? $dayNumber + 1 : null;

        $userNote = \Modules\Bible\App\Models\BibleUserNote::where('user_id', Auth::id())
            ->where('plan_day_id', $day->id)
            ->first();

        // Load user favorites for this day's verses to avoid N+1 queries
        $verseIds = [];
        foreach ($day->contents as $content) {
            if ($content->type === 'scripture' && $content->verses) {
                // flatten() is needed because verses are grouped by chapter
                $verseIds = array_merge($verseIds, $content->verses->flatten()->pluck('id')->toArray());
            }
        }

        $userFavorites = [];
        if (! empty($verseIds)) {
            $userFavorites = \Modules\Bible\App\Models\BibleFavorite::where('user_id', Auth::id())
                ->whereIn('verse_id', $verseIds)
                ->get()
                ->keyBy('verse_id');
        }

        // Pass versions for selector
        $versions = \Modules\Bible\App\Models\BibleVersion::where('is_active', true)->orderBy('name')->get();

        return view('bible::memberpanel.plans.reader', compact('subscription', 'day', 'isCompleted', 'isLocked', 'prevDay', 'nextDay', 'userNote', 'versions', 'targetVersion', 'userFavorites'));
    }

    /**
     * Idempotent check-in: mark day as read. Double-click or duplicate POSTs do not create duplicate progress.
     */
    public function complete(Request $request, $subscriptionId, $dayId)
    {
        $subscription = BiblePlanSubscription::where('user_id', Auth::id())->findOrFail($subscriptionId);
        $day = BiblePlanDay::where('plan_id', $subscription->plan_id)->findOrFail($dayId);

        BibleUserProgress::firstOrCreate(
            [
                'subscription_id' => $subscription->id,
                'plan_day_id' => $day->id,
            ],
            [
                'completed_at' => now(),
            ]
        );

        UserReadingLog::firstOrCreate(
            [
                'subscription_id' => $subscription->id,
                'plan_day_id' => $day->id,
            ],
            [
                'user_id' => Auth::id(),
                'day_number' => $day->day_number,
                'completed_at' => now(),
            ]
        );

        app(BadgeService::class)->evaluateAfterCompletion($subscription, $day);

        if ($subscription->current_day_number == $day->day_number) {
            $nextDay = $day->day_number + 1;
            if ($nextDay <= $subscription->plan->days()->count()) {
                $subscription->update(['current_day_number' => $nextDay]);
            } else {
                $subscription->update(['is_completed' => true]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'completed' => true]);
        }

        return redirect()->route('member.bible.reader.congratulations', [$subscription->id, $day->id]);
    }

    /**
     * Remove the completion of a day (undo "Lido"). Safe action for when the user clicked by mistake.
     */
    public function uncomplete(Request $request, $subscriptionId, $dayId)
    {
        $subscription = BiblePlanSubscription::where('user_id', Auth::id())->findOrFail($subscriptionId);
        $day = BiblePlanDay::where('plan_id', $subscription->plan_id)->findOrFail($dayId);

        BibleUserProgress::where('subscription_id', $subscription->id)
            ->where('plan_day_id', $day->id)
            ->delete();

        UserReadingLog::where('subscription_id', $subscription->id)
            ->where('plan_day_id', $day->id)
            ->delete();

        if ($subscription->current_day_number > $day->day_number) {
            $subscription->update(['current_day_number' => $day->day_number]);
        }
        if ($subscription->is_completed) {
            $subscription->update(['is_completed' => false]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'completed' => false]);
        }

        return back()->with('success', 'Leitura desmarcada.');
    }

    public function congratulations($subscriptionId, $dayId)
    {
        $subscription = BiblePlanSubscription::with('plan')->where('user_id', Auth::id())->findOrFail($subscriptionId);
        $day = BiblePlanDay::where('plan_id', $subscription->plan_id)->findOrFail($dayId);

        // Calculate next day
        $nextDayNum = $day->day_number + 1;
        $totalDays = $subscription->plan->days()->count();
        $nextDay = ($nextDayNum <= $totalDays) ? $nextDayNum : null;

        return view('bible::memberpanel.plans.congratulations', compact('subscription', 'day', 'nextDay'));
    }

    public function storeNote(Request $request, $subscriptionId, $dayId)
    {
        $request->validate([
            'note_content' => 'required|string',
        ]);

        \Modules\Bible\App\Models\BibleUserNote::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'plan_day_id' => $dayId,
            ],
            [
                'note_content' => $request->note_content,
                'color_code' => $request->input('color_code', '#ffee00'),
            ]
        );

        return back()->with('success', 'Anotação salva.');
    }
}
