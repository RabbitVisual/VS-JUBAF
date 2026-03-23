<?php

namespace Modules\Bible\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource for GET /find response (reference string lookup).
 * Keeps compatibility with Intercessor room (verses, full_chapter_url).
 */
class FindByReferenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'reference' => $this->resource['reference'],
            'book' => $this->resource['book'],
            'chapter' => $this->resource['chapter'],
            'verses' => VerseResource::collection($this->resource['verses']),
            'full_chapter_url' => $this->resource['full_chapter_url'],
        ];
    }
}
