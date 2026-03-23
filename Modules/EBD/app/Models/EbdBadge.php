<?php

namespace Modules\EBD\App\Models;

use Illuminate\Database\Eloquent\Model;

class EbdBadge extends Model
{
    protected $table = 'ebd_badges';

    protected $fillable = [
        'slug',
        'name',
        'description',
        'icon',
        'icon_path',
        'is_hidden',
    ];

    protected $casts = [
        'is_hidden' => 'boolean',
    ];
}
