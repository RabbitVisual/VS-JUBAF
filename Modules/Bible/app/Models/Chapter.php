<?php

namespace Modules\Bible\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chapter extends Model
{
    protected $fillable = [
        'book_id',
        'chapter_number',
        'total_verses',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function verses(): HasMany
    {
        return $this->hasMany(Verse::class)->orderBy('verse_number');
    }

    public function getFullReferenceAttribute(): string
    {
        return $this->book->name.' '.$this->chapter_number;
    }
}
