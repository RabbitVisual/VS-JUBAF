<?php

namespace Modules\Worship\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Bible\App\Models\Verse;

class RandomVerseController extends Controller
{
    public function __invoke(): JsonResponse
    {
        // Get a random verse
        // Optimally, we might want to filter by "favorites" or "popular" later, but for now random is fine.
        // We need to ensure we have a Bible version.
        // Assuming there is content in the database.

        $verse = Verse::with(['chapter.book'])->inRandomOrder()->first();

        if (!$verse) {
            return response()->json([
                'text' => 'Pois onde dois ou três se reunirem em meu nome, ali eu estou no meio deles.',
                'reference' => 'Mateus 18:20'
            ]);
        }

        $chapter = $verse->chapter;
        $book = $chapter->book;

        return response()->json([
            'text' => $verse->text,
            'reference' => "{$book->name} {$chapter->chapter_number}:{$verse->verse_number}"
        ]);
    }
}
