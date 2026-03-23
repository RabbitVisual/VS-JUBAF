<?php

namespace Modules\Sermons\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BibleCommentary extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'book',
        'chapter',
        'verse_start',
        'verse_end',
        'title',
        'content',
        'user_id',
        'status',
        'is_official',
        'cover_image',
        'audio_path',
        'audio_url',
    ];

    protected $casts = [
        'is_official' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the audio source (file or URL)
     */
    public function getAudioSourceAttribute()
    {
        if ($this->audio_path) {
            return asset('storage/'.$this->audio_path);
        }

        return $this->audio_url;
    }

    // Helper to get formatted reference e.g. "Genesis 1:1-5"
    public function getReferenceAttribute()
    {
        $ref = "{$this->book} {$this->chapter}:{$this->verse_start}";
        if ($this->verse_end && $this->verse_end != $this->verse_start) {
            $ref .= "-{$this->verse_end}";
        }

        return $ref;
    }

    /**
     * Get the cover image URL
     */
    public function getCoverUrlAttribute(): string
    {
        if ($this->cover_image) {
            return asset('storage/'.$this->cover_image);
        }

        return 'https://images.unsplash.com/photo-1473170611423-22489201d919?q=80&w=1200&auto=format&fit=crop&text='.urlencode('Bible Commentary');
    }
}
