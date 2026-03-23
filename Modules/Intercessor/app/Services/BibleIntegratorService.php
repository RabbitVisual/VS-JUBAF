<?php

namespace Modules\Intercessor\App\Services;

use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Models\Chapter;
use Modules\Bible\App\Models\Verse;

class BibleIntegratorService
{
    /**
     * Parse a reference string (e.g. "John 3:16") and return the Verse model.
     */
    public function getVerse(string $reference): ?Verse
    {
        // Simple parser for "Book Chapter:Verse" format
        // Example: "João 3:16", "Genesis 1:1"

        // Regex to capture Book Name, Chapter Number, Verse Number
        // Supports spaces in book names (e.g. "1 John")
        if (! preg_match('/^(.+?)\s+(\d+):(\d+)$/', trim($reference), $matches)) {
            return null;
        }

        $bookName = $matches[1];
        $chapterNum = $matches[2];
        $verseNum = $matches[3];

        // Find Book (Like search to handle partials or accents if DB allows)
        $book = Book::where('name', 'like', $bookName)
            ->orWhere('name', 'like', "%{$bookName}%")
            ->first();

        if (! $book) {
            return null;
        }

        // Find Chapter
        $chapter = Chapter::where('book_id', $book->id)
            ->where('chapter_number', $chapterNum)
            ->first();

        if (! $chapter) {
            return null;
        }

        // Find Verse
        return Verse::where('chapter_id', $chapter->id)
            ->where('verse_number', $verseNum)
            ->first();
    }

    /**
     * Search passages by text.
     */
    public function search(string $text)
    {
        return Verse::search($text)->take(5)->get();
    }
}
