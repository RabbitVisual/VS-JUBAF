<?php

declare(strict_types=1);

namespace Modules\Gamification\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReading extends Model
{
    protected $table = 'gamification_daily_readings';

    protected $fillable = [
        'date',
        'book_number',
        'chapter_number',
        'bible_version_abbreviation',
        'book_name',
        'title',
        'intro_text',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
