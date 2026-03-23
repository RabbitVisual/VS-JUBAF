<?php

namespace Modules\Worship\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Worship\App\Enums\MusicalKey;

class WorshipSong extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'artist',
        'bpm',
        'time_signature',
        'original_key',
        'content_chordpro',
        'lyrics_only',
        'youtube_id',
        'themes',
        'multitrack_url',
        'song_structure',
    ];

    protected $casts = [
        'original_key' => MusicalKey::class,
        'themes' => 'array',
        'bpm' => 'integer',
    ];

    public function getYoutubeUrlAttribute(): ?string
    {
        return $this->youtube_id ? "https://www.youtube.com/watch?v={$this->youtube_id}" : null;
    }

    public function regenerateLyrics(): void
    {
        if (!$this->content_chordpro) {
            $this->lyrics_only = null;
        } else {
            // Remove chords [G], [Am7], etc.
            $lyrics = preg_replace('/\[[^\]]+\]/', '', $this->content_chordpro);
            // Clean up extra spaces that might be left behind
            $lyrics = preg_replace('/ +/', ' ', $lyrics);
            $this->lyrics_only = trim($lyrics);
        }

        $this->save();
    }
}
