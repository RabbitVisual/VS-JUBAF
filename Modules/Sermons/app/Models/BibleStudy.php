<?php

namespace Modules\Sermons\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BibleStudy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'description',
        'content',
        'cover_image',
        'video_url',
        'audio_url',
        'audio_file',
        'series_id',
        'category_id',
        'user_id',
        'status',
        'visibility',
        'is_featured',
        'views',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    /**
     * Scope for featured studies
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get the audio source (file or URL)
     */
    public function getAudioSourceAttribute()
    {
        if ($this->audio_file) {
            return asset('storage/'.$this->audio_file);
        }

        return $this->audio_url;
    }

    public function series()
    {
        return $this->belongsTo(BibleSeries::class);
    }

    public function category()
    {
        return $this->belongsTo(SermonCategory::class, 'category_id');
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
        if ($this->cover_image) {
            return asset('storage/'.$this->cover_image);
        }

        $category = $this->category?->name ?? 'Bible Study';

        return 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?q=80&w=1200&auto=format&fit=crop&text='.urlencode($category);
    }
}
