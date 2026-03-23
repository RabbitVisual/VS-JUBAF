<?php

namespace Modules\Bible\App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Bible\App\Models\BibleBookPanorama;
use Modules\Bible\App\Models\BibleVersion;
use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Models\Chapter;
use Modules\Bible\App\Models\Verse;

class BibleApiService
{
    private const CACHE_TTL_SECONDS = 3600;

    private const CACHE_PREFIX = 'bible_api_';

    /**
     * Get active Bible versions (cached).
     *
     * @return Collection<int, BibleVersion>
     */
    public function getVersions(): Collection
    {
        return Cache::remember(self::CACHE_PREFIX.'versions', self::CACHE_TTL_SECONDS, function () {
            return BibleVersion::where('is_active', true)
                ->orderByRaw('is_default DESC')
                ->orderBy('name')
                ->get(['id', 'name', 'abbreviation', 'is_default']);
        });
    }

    /**
     * Get books for a version (cached by version_id).
     *
     * @return Collection<int, Book>
     */
    public function getBooks(?int $versionId = null): Collection
    {
        $versionId = $versionId ?? $this->getDefaultVersionId();
        if (! $versionId) {
            return collect();
        }

        return Cache::remember(self::CACHE_PREFIX."books_{$versionId}", self::CACHE_TTL_SECONDS, function () use ($versionId) {
            return Book::where('bible_version_id', $versionId)
                ->orderBy('book_number')
                ->get(['id', 'name', 'abbreviation', 'book_number', 'testament']);
        });
    }

    /**
     * Get chapters for a book. Supports book_id or (book_name + version_id).
     *
     * @return Collection<int, Chapter>
     */
    public function getChapters(?int $bookId = null, ?string $bookName = null, ?int $versionId = null): Collection
    {
        if ($bookId) {
            return Cache::remember(self::CACHE_PREFIX."chapters_{$bookId}", self::CACHE_TTL_SECONDS, function () use ($bookId) {
                return Chapter::where('book_id', $bookId)
                    ->orderBy('chapter_number')
                    ->get(['id', 'chapter_number', 'total_verses']);
            });
        }

        if ($bookName && $versionId) {
            $book = Book::where('bible_version_id', $versionId)
                ->where('name', $bookName)
                ->first();
            if ($book) {
                return $this->getChapters($book->id, null, null);
            }
        }

        return collect();
    }

    /**
     * Get verses for a chapter. Optional verse_range e.g. "1-5" or "1,3,5-10".
     *
     * @return Collection<int, Verse>
     */
    public function getVerses(
        ?int $chapterId = null,
        ?int $bookId = null,
        ?int $chapterNumber = null,
        ?string $verseRange = null
    ): Collection {
        if ($chapterId === null && $bookId && $chapterNumber) {
            $chapter = Chapter::where('book_id', $bookId)
                ->where('chapter_number', $chapterNumber)
                ->first();
            $chapterId = $chapter?->id;
        }

        if (! $chapterId) {
            return collect();
        }

        $query = Verse::where('chapter_id', $chapterId)->orderBy('verse_number');

        if ($verseRange !== null && $verseRange !== '') {
            $ranges = $this->parseVerseRange($verseRange);
            if ($ranges !== []) {
                $query->where(function ($q) use ($ranges) {
                    foreach ($ranges as $range) {
                        if (is_array($range)) {
                            $q->orWhereBetween('verse_number', $range);
                        } else {
                            $q->orWhere('verse_number', $range);
                        }
                    }
                });
            }
        }

        return $query->get(['id', 'verse_number', 'text']);
    }

    /**
     * Find by reference string (e.g. "João 3:16" or "Salmos 23:1-3").
     *
     * @return array{reference: string, book: string, chapter: int, verses: Collection, full_chapter_url: string}|null
     */
    public function findByReference(string $reference): ?array
    {
        $reference = trim($reference);
        if ($reference === '') {
            return null;
        }

        if (! preg_match('/^(.+?)\s+(\d+):(\d+)(?:-(\d+))?$/u', $reference, $matches)) {
            return null;
        }

        $bookName = trim($matches[1]);
        $chapterNum = (int) $matches[2];
        $verseStart = (int) $matches[3];
        $verseEnd = (int) ($matches[4] ?? $verseStart);

        $book = Book::where('name', 'like', $bookName)
            ->orWhere('abbreviation', 'like', $bookName)
            ->with('bibleVersion')
            ->first();

        if (! $book) {
            return null;
        }

        $chapter = Chapter::where('book_id', $book->id)
            ->where('chapter_number', $chapterNum)
            ->first();

        if (! $chapter) {
            return null;
        }

        $verses = Verse::where('chapter_id', $chapter->id)
            ->whereBetween('verse_number', [$verseStart, $verseEnd])
            ->orderBy('verse_number')
            ->get(['id', 'verse_number', 'text']);

        if ($verses->isEmpty()) {
            return null;
        }

        $refStr = $book->name.' '.$chapter->chapter_number.':'.$verseStart;
        if ($verseEnd !== $verseStart) {
            $refStr .= '-'.$verseEnd;
        }

        $fullChapterUrl = route('memberpanel.bible.chapter', [
            'version' => $book->bibleVersion->abbreviation,
            'book' => $book->book_number,
            'chapter' => $chapter->chapter_number,
        ]);

        return [
            'reference' => $refStr,
            'book' => $book->name,
            'book_number' => $book->book_number,
            'chapter' => $chapter->chapter_number,
            'verses' => $verses,
            'full_chapter_url' => $fullChapterUrl,
        ];
    }

    /**
     * Search verses by text (LIKE).
     *
     * @return Collection<int, Verse>
     */
    public function search(string $query, int $limit = 10): Collection
    {
        $query = trim($query);
        if ($query === '') {
            return collect();
        }

        return Verse::search($query)
            ->take($limit)
            ->get();
    }

    /**
     * Get one random verse. Optional version_id to restrict to a version.
     */
    public function getRandomVerse(?int $versionId = null): ?Verse
    {
        $q = Verse::with('chapter.book')->inRandomOrder();

        if ($versionId) {
            $q->whereHas('chapter.book', fn ($b) => $b->where('bible_version_id', $versionId));
        }

        return $q->first();
    }

    /**
     * Compare verse(s) between two versions. v1/v2 can be version id or abbreviation.
     *
     * @param int|string $v1 version id or abbreviation
     * @param int|string $v2 version id or abbreviation
     * @return array{v1: array{abbreviation: string, name: string, verses: Collection}, v2: array{abbreviation: string, name: string, verses: Collection}}|null
     */
    public function compare($v1, $v2, int $bookNumber, int $chapter, ?int $verse = null): ?array
    {
        $version1 = is_int($v1) || ctype_digit((string) $v1)
            ? BibleVersion::find((int) $v1)
            : BibleVersion::where('abbreviation', $v1)->first();
        $version2 = is_int($v2) || ctype_digit((string) $v2)
            ? BibleVersion::find((int) $v2)
            : BibleVersion::where('abbreviation', $v2)->first();

        if (! $version1 || ! $version2) {
            return null;
        }

        $book1 = Book::where('bible_version_id', $version1->id)->where('book_number', $bookNumber)->first();
        $book2 = Book::where('bible_version_id', $version2->id)->where('book_number', $bookNumber)->first();

        if (! $book1 || ! $book2) {
            return null;
        }

        $query1 = Verse::select('verses.*')
            ->join('chapters', 'verses.chapter_id', '=', 'chapters.id')
            ->where('chapters.book_id', $book1->id)
            ->where('chapters.chapter_number', $chapter);

        $query2 = Verse::select('verses.*')
            ->join('chapters', 'verses.chapter_id', '=', 'chapters.id')
            ->where('chapters.book_id', $book2->id)
            ->where('chapters.chapter_number', $chapter);

        if ($verse !== null) {
            $query1->where('verses.verse_number', $verse);
            $query2->where('verses.verse_number', $verse);
        } else {
            $query1->orderBy('verses.verse_number');
            $query2->orderBy('verses.verse_number');
        }

        return [
            'v1' => [
                'abbreviation' => $version1->abbreviation,
                'name' => $version1->name,
                'verses' => $query1->get(),
            ],
            'v2' => [
                'abbreviation' => $version2->abbreviation,
                'name' => $version2->name,
                'verses' => $query2->get(),
            ],
        ];
    }

    /**
     * Get chapter audio URL for a version (if template is configured).
     *
     * @param int|string $version version id or abbreviation
     * @return string|null URL or null if no template
     */
    public function getChapterAudioUrl(int|string $version, int $bookNumber, int $chapterNumber): ?string
    {
        $versionModel = is_int($version) || ctype_digit((string) $version)
            ? BibleVersion::find((int) $version)
            : BibleVersion::where('abbreviation', $version)->first();

        if (! $versionModel) {
            return null;
        }

        return $versionModel->getChapterAudioUrl($bookNumber, $chapterNumber);
    }

    /**
     * Get book panorama (author, date, theme, recipients) by canonical book number.
     *
     * @return array{author: string|null, date_written: string|null, theme_central: string|null, recipients: string|null}|null
     */
    public function getPanoramaByBookNumber(int $bookNumber, ?string $language = 'pt'): ?array
    {
        $bookNumber = max(1, min(66, $bookNumber));
        $language = $language ?: 'pt';

        $panorama = BibleBookPanorama::where('book_number', $bookNumber)
            ->where('language', $language)
            ->first();

        if (! $panorama) {
            return null;
        }

        return [
            'author' => $panorama->author,
            'date_written' => $panorama->date_written,
            'theme_central' => $panorama->theme_central,
            'recipients' => $panorama->recipients,
        ];
    }

    /**
     * Invalidate known Bible caches (call after import/update).
     * For full flush use Cache::flush() when using Redis with bible_api_* keys.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_PREFIX.'versions');
        // Books/chapters keys are per-id; without tag support we forget only versions.
        // Consider using Redis with tags in production for full invalidation.
    }

    private function getDefaultVersionId(): ?int
    {
        $v = BibleVersion::where('is_active', true)
            ->orderByRaw('is_default DESC')
            ->first();

        return $v?->id;
    }

    /**
     * @return array<int|array{0: int, 1: int}>
     */
    private function parseVerseRange(string $verseRange): array
    {
        $ranges = [];
        $parts = explode(',', $verseRange);

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }
            if (str_contains($part, '-')) {
                $segments = explode('-', $part, 2);
                $start = (int) trim($segments[0]);
                $end = (int) trim($segments[1]);
                if ($start > 0 && $end >= $start) {
                    $ranges[] = [$start, $end];
                }
            } else {
                $num = (int) $part;
                if ($num > 0) {
                    $ranges[] = $num;
                }
            }
        }

        return $ranges;
    }
}
