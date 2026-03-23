<?php

namespace Modules\Bible\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Verse extends Model
{
    protected $fillable = [
        'chapter_id',
        'verse_number',
        'text',
        'original_verse_id',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'bible_favorites',
            'verse_id',
            'user_id'
        )->withPivot('color')->withTimestamps();
    }

    public function getFullReferenceAttribute(): string
    {
        $chapter = $this->chapter;
        $book = $chapter->book;

        return $book->name.' '.$chapter->chapter_number.':'.$this->verse_number;
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where('text', 'like', "%{$search}%");
    }
}
