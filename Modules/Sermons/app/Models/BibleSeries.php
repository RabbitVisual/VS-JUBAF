<?php

namespace Modules\Sermons\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BibleSeries extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'status',
        'is_featured',
        'user_id',
    ];

    /**
     * Scope for featured series
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function sermons()
    {
        return $this->hasMany(Sermon::class, 'series_id');
    }

    public function studies()
    {
        return $this->hasMany(BibleStudy::class, 'series_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cover image URL
     */
    public function getCoverUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/'.$this->image);
        }

        return 'https://images.unsplash.com/photo-1542281286-9e0a16bb7366?q=80&w=1200&auto=format&fit=crop&text='.urlencode('Bible Series');
    }
}
