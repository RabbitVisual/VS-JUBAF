<?php

namespace Modules\Bible\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiblePlanContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_day_id',
        'order_index',
        'type', // scripture, devotional, video
        'title',
        'body', // HTML or Video URL
        'book_id',
        'chapter_start',
        'chapter_end',
        'verse_start',
        'verse_end',
    ];

    public function day()
    {
        return $this->belongsTo(BiblePlanDay::class, 'plan_day_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
