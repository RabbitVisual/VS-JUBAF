<?php

declare(strict_types=1);

namespace Modules\Gamification\App\Services;

use Modules\Bible\App\Models\BibleVersion;
use Modules\Bible\App\Models\Verse;

/**
 * Recomenda um versículo para o bot Elias. Usa apenas o módulo Bible local.
 * Retorna referência, texto e URL direta ao versículo no painel.
 */
class VerseRecommendationService
{
    /**
     * Retorna um versículo aleatório (edificante) para o bot exibir com link.
     *
     * @return array{reference: string, text: string, url: string, book_name: string, chapter: int, verse_number: int, version: string}|null
     */
    public function getRandomVerseForBot(): ?array
    {
        $version = BibleVersion::default()->first() ?? BibleVersion::active()->first();
        if (! $version) {
            return null;
        }

        $verse = Verse::query()
            ->with(['chapter.book'])
            ->whereHas('chapter.book', fn ($q) => $q->where('bible_version_id', $version->id))
            ->where('text', '!=', '')
            ->inRandomOrder()
            ->first();

        if (! $verse) {
            return null;
        }

        $verse->loadMissing('chapter.book.bibleVersion');
        $chapter = $verse->chapter;
        $book = $chapter->book;
        $versionModel = $book->bibleVersion ?? $version;

        $url = route('memberpanel.bible.chapter', [
            'version' => $versionModel->abbreviation,
            'book' => $book->book_number,
            'chapter' => $chapter->chapter_number,
        ]) . '#verse-' . $verse->verse_number;

        $reference = $book->name . ' ' . $chapter->chapter_number . ':' . $verse->verse_number;

        return [
            'reference' => $reference,
            'text' => $verse->text,
            'url' => $url,
            'book_name' => $book->name,
            'chapter' => $chapter->chapter_number,
            'verse_number' => $verse->verse_number,
            'version' => $versionModel->abbreviation,
        ];
    }
}
