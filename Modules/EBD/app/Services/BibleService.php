<?php

namespace Modules\EBD\App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Models\BibleVersion;
use Modules\Bible\App\Services\BibleApiService;

class BibleService
{
    public function __construct(
        private ?BibleApiService $bibleApi = null
    ) {
        $this->bibleApi = $this->bibleApi ?? (class_exists(BibleApiService::class) ? app(BibleApiService::class) : null);
    }

    /**
     * Get Bible verses by book, chapter and optional verses
     */
    public function getVerses(string $book, int $chapter, ?string $version = 'nvi', ?string $verses = null): array
    {
        try {
            if ($this->bibleApi !== null && class_exists(BibleVersion::class)) {
                $versionModel = BibleVersion::where('abbreviation', strtoupper((string) $version))
                    ->orWhere('abbreviation', $version)
                    ->first();

                if ($versionModel) {
                    $bookModel = Book::where('bible_version_id', $versionModel->id)
                        ->where('name', $book)
                        ->first();

                    if ($bookModel) {
                        $collection = $this->bibleApi->getVerses(
                            null,
                            $bookModel->id,
                            $chapter,
                            $verses
                        );
                        return $collection->map(fn ($v) => (object) [
                            'verse' => $v->verse_number,
                            'text' => $v->text,
                        ])->values()->all();
                    }
                }
            }

            // Fallback to old structure (bible_verses table)
            $query = DB::table('bible_verses')
                ->where('book', $book)
                ->where('chapter', $chapter)
                ->where('version', $version)
                ->orderBy('verse');

            // Filter by specific verses if provided (e.g., "1-10" or "1,3,5-10")
            if ($verses) {
                $verseRanges = $this->parseVerseRange($verses);
                if (! empty($verseRanges)) {
                    $query->where(function ($q) use ($verseRanges) {
                        foreach ($verseRanges as $range) {
                            if (is_array($range)) {
                                $q->orWhereBetween('verse', $range);
                            } else {
                                $q->orWhere('verse', $range);
                            }
                        }
                    });
                }
            }

            return $query->get()->toArray();
        } catch (\Exception $e) {
            Log::warning('Bible module not available or error fetching verses', [
                'book' => $book,
                'chapter' => $chapter,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get Bible books list (usa BibleApiService quando disponível para cache).
     */
    public function getBooks(?int $versionId = null): array
    {
        try {
            if ($this->bibleApi !== null) {
                $books = $this->bibleApi->getBooks($versionId);
                return $books->map(fn ($b) => [
                    'id' => $b->id,
                    'name' => $b->name,
                    'abbreviation' => $b->abbreviation,
                ])->values()->all();
            }
            if (class_exists('\Modules\Bible\App\Models\Book')) {
                $query = \Modules\Bible\App\Models\Book::query();
                if ($versionId) {
                    $query->where('bible_version_id', $versionId);
                } else {
                    $defaultVersion = \Modules\Bible\App\Models\BibleVersion::default()->first()
                        ?? \Modules\Bible\App\Models\BibleVersion::active()->first();
                    if ($defaultVersion) {
                        $query->where('bible_version_id', $defaultVersion->id);
                    }
                }
                return $query->ordered()->get(['id', 'name', 'abbreviation'])->toArray();
            }
            return DB::table('bible_books')->select('name')->distinct()->orderBy('name')->pluck('name')->toArray();
        } catch (\Exception $e) {
            Log::warning('Bible module not available or error fetching books', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get available Bible versions (usa BibleApiService quando disponível para cache).
     */
    public function getVersions(): array
    {
        try {
            if ($this->bibleApi !== null) {
                return $this->bibleApi->getVersions()->toArray();
            }
            if (class_exists('\Modules\Bible\App\Models\BibleVersion')) {
                return \Modules\Bible\App\Models\BibleVersion::active()
                    ->orderBy('is_default', 'desc')
                    ->orderBy('name')
                    ->get(['id', 'name', 'abbreviation', 'is_default'])
                    ->toArray();
            }
            return DB::table('bible_verses')->select('version')->distinct()->orderBy('version')->pluck('version')->toArray();
        } catch (\Exception $e) {
            return ['nvi', 'acf', 'ara', 'kjv'];
        }
    }

    /**
     * Get chapters for a book (usa BibleApiService quando disponível para cache).
     */
    public function getChapters(int $bookId): array
    {
        try {
            if ($this->bibleApi !== null) {
                return $this->bibleApi->getChapters($bookId, null, null)->toArray();
            }
            if (class_exists('\Modules\Bible\App\Models\Chapter')) {
                return \Modules\Bible\App\Models\Chapter::where('book_id', $bookId)
                    ->orderBy('chapter_number')
                    ->get(['id', 'chapter_number', 'total_verses'])
                    ->toArray();
            }
            return [];
        } catch (\Exception $e) {
            Log::warning('Error fetching chapters', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Parse verse range string (e.g., "1-10" or "1,3,5-10")
     */
    private function parseVerseRange(string $verses): array
    {
        $ranges = [];
        $parts = explode(',', $verses);

        foreach ($parts as $part) {
            $part = trim($part);

            if (strpos($part, '-') !== false) {
                [$start, $end] = explode('-', $part);
                $ranges[] = [(int) trim($start), (int) trim($end)];
            } else {
                $ranges[] = (int) trim($part);
            }
        }

        return $ranges;
    }

    /**
     * Check if Bible module is available
     */
    public function isAvailable(): bool
    {
        try {
            return DB::getSchemaBuilder()->hasTable('bible_verses')
                && DB::getSchemaBuilder()->hasTable('bible_books');
        } catch (\Exception $e) {
            return false;
        }
    }
}
