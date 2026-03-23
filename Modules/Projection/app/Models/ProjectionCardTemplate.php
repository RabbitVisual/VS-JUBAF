<?php

namespace Modules\Projection\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProjectionCardTemplate extends Model
{
    protected $table = 'projection_card_templates';

    protected $fillable = [
        'name',
        'slug',
        'title_label',
        'subtitle_label',
        'background_type',
        'background_value',
        'font_family',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (ProjectionCardTemplate $template) {
            if (empty($template->slug)) {
                $template->slug = Str::slug($template->name);
            }
        });
    }
}
