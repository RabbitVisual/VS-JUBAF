<?php

namespace Modules\EBD\App\Models;

use Illuminate\Database\Eloquent\Model;

class EbdLearningPath extends Model
{
    protected $table = 'ebd_learning_paths';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
