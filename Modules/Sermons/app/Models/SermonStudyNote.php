<?php

namespace Modules\Sermons\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Models\Chapter;

class SermonStudyNote extends Model
{
    protected $table = 'sermon_study_notes';

    protected $fillable = [
        'user_id',
        'sermon_id',
        'reference_text',
        'book_id',
        'chapter_id',
        'content',
        'is_global',
    ];

    protected $casts = [
        'is_global' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sermon(): BelongsTo
    {
        return $this->belongsTo(Sermon::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }
}
