<?php

namespace Modules\Bible\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Book extends Model
{
    protected $fillable = [
        'bible_version_id',
        'name',
        'book_number',
        'abbreviation',
        'testament',
        'total_chapters',
        'total_verses',
        'order',
    ];

    public function bibleVersion(): BelongsTo
    {
        return $this->belongsTo(BibleVersion::class);
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('chapter_number');
    }

    public function verses(): HasManyThrough
    {
        return $this->hasManyThrough(
            Verse::class,
            Chapter::class,
            'book_id',    // Foreign key on chapters table
            'chapter_id', // Foreign key on verses table
            'id',         // Local key on books table
            'id'          // Local key on chapters table
        );
    }

    public function scopeOldTestament($query)
    {
        return $query->where('testament', 'old');
    }

    public function scopeNewTestament($query)
    {
        return $query->where('testament', 'new');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('book_number');
    }
}
