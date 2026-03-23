<?php

namespace Modules\Bible\App\Models;

use Illuminate\Database\Eloquent\Model;

class BiblePlanTemplate extends Model
{
    protected $table = 'bible_plan_templates';

    protected $fillable = [
        'key',
        'name',
        'description',
        'complexity',
        'order_type',
        'options',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean',
    ];
}
