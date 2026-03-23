<?php

namespace Modules\Bible\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BibleMetadata extends Model
{
    protected $table = 'bible_metadata';

    protected $fillable = [
        'bible_version_id',
        'book_id',
        'chapter_number',
        'verse_count',
    ];

    protected $casts = [
        'verse_count' => 'integer',
    ];

    public function bibleVersion(): BelongsTo
    {
        return $this->belongsTo(BibleVersion::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
