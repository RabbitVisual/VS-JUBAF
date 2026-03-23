<?php

namespace Modules\EBD\App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalIcon extends Model
{
    protected $table = 'global_icons';

    protected $fillable = [
        'slug',
        'fa_name',
        'style',
        'category',
        'label',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Resolve icon for Blade: fa_name + style for <x-icon name="..." style="...">.
     */
    public function getFaNameAttribute($value): string
    {
        return $value ?? $this->slug;
    }
}
