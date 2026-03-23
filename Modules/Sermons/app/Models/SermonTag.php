<?php

namespace Modules\Sermons\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SermonTag extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'color',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    /**
     * Get sermons with this tag
     */
    public function sermons(): BelongsToMany
    {
        return $this->belongsToMany(Sermon::class, 'sermon_tag', 'sermon_tag_id', 'sermon_id');
    }
}
