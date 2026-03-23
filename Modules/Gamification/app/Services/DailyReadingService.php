<?php

declare(strict_types=1);

namespace Modules\Gamification\App\Services;

use Carbon\Carbon;
use Modules\Bible\App\Models\BibleVersion;
use Modules\Bible\App\Models\Book;
use Modules\Gamification\Models\DailyReading;

/**
 * Recomendação de leitura do dia (24h). Usa apenas o módulo Bible local.
 */
class DailyReadingService
{
    /**
     * Retorna a recomendação para a data. Se não existir, gera e persiste de forma determinística.
     *
     * @return array{title: string, url: string, version: string, book: int, chapter: int, book_name: string}|null
     */
    public function getDailyReadingForDate(Carbon $date): ?array
    {
        $date = $date->copy()->startOfDay();
        $record = DailyReading::whereDate('date', $date)->first();

        if ($record) {
            return $this->formatResult(
                $record->title,
                $record->bible_version_abbreviation,
                $record->book_number,
                $record->chapter_number,
                $record->book_name
            );
        }

        $generated = $this->generateAndStoreForDate($date);
        if (! $generated) {
            return null;
        }

        return $this->formatResult(
            $generated['title'],
            $generated['bible_version_abbreviation'],
            $generated['book_number'],
            $generated['chapter_number'],
            $generated['book_name']
        );
    }

    /**
     * Gera e persiste a recomendação para a data usando apenas o módulo Bible.
     */
    public function generateAndStoreForDate(Carbon $date): ?array
    {
        $version = BibleVersion::default()->first() ?? BibleVersion::active()->first();
        if (! $version) {
            return null;
        }

        $chapters = $this->getAllChaptersList($version);
        if ($chapters === []) {
            return null;
        }

        $index = abs(crc32($date->format('Y-m-d'))) % count($chapters);
        $ch = $chapters[$index];

        DailyReading::updateOrCreate(
            ['date' => $date->format('Y-m-d')],
            [
                'book_number' => $ch['book_number'],
                'chapter_number' => $ch['chapter_number'],
                'bible_version_abbreviation' => $version->abbreviation,
                'book_name' => $ch['book_name'],
                'title' => $ch['book_name'] . ' ' . $ch['chapter_number'],
                'intro_text' => null,
            ]
        );

        return [
            'title' => $ch['book_name'] . ' ' . $ch['chapter_number'],
            'bible_version_abbreviation' => $version->abbreviation,
            'book_number' => $ch['book_number'],
            'chapter_number' => $ch['chapter_number'],
            'book_name' => $ch['book_name'],
        ];
    }

    /**
     * Lista [book_number, chapter_number, book_name] para uma versão (ordem de leitura).
     */
    private function getAllChaptersList(BibleVersion $version): array
    {
        $books = Book::where('bible_version_id', $version->id)
            ->orderBy('book_number')
            ->get();

        $list = [];
        foreach ($books as $book) {
            foreach ($book->chapters()->orderBy('chapter_number')->pluck('chapter_number') as $chapterNumber) {
                $list[] = [
                    'book_number' => $book->book_number,
                    'chapter_number' => $chapterNumber,
                    'book_name' => $book->name,
                ];
            }
        }
        return $list;
    }

    private function formatResult(
        string $title,
        string $versionAbbr,
        int $bookNumber,
        int $chapterNumber,
        string $bookName
    ): array {
        $url = route('memberpanel.bible.chapter', [
            'version' => $versionAbbr,
            'book' => $bookNumber,
            'chapter' => $chapterNumber,
        ]);

        return [
            'title' => $title,
            'url' => $url,
            'version' => $versionAbbr,
            'book' => $bookNumber,
            'chapter' => $chapterNumber,
            'book_name' => $bookName,
        ];
    }
}
