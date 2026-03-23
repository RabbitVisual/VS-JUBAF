<?php

namespace Modules\Bible\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource for GET /random response (Projection screen compatibility).
 */
class RandomVerseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $verse = $this->resource;
        $chapter = $verse->chapter;
        $book = $chapter->book;

        return [
            'id' => $verse->id,
            'verse_number' => $verse->verse_number,
            'text' => $verse->text,
            'chapter' => [
                'id' => $chapter->id,
                'chapter_number' => $chapter->chapter_number,
            ],
            'book' => [
                'id' => $book->id,
                'name' => $book->name,
                'abbreviation' => $book->abbreviation,
                'book_number' => $book->book_number,
            ],
        ];
    }
}
