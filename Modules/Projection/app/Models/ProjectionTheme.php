<?php

namespace Modules\Projection\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProjectionTheme extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'background_type',
        'background_value',
        'font_family',
        'font_size_base',
        'text_color',
        'text_shadow',
        'alignment',
        'padding',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (ProjectionTheme $theme) {
            if (empty($theme->slug)) {
                $theme->slug = Str::slug($theme->name);
            }
        });
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
