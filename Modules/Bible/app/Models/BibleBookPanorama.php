<?php

namespace Modules\Bible\App\Models;

use Illuminate\Database\Eloquent\Model;

class BibleBookPanorama extends Model
{
    protected $table = 'bible_book_panoramas';

    protected $fillable = [
        'book_number',
        'testament',
        'author',
        'date_written',
        'theme_central',
        'recipients',
        'language',
    ];
}
