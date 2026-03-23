<?php

namespace Modules\Worship\App\Services;

use Illuminate\Support\Facades\Http;

class LyricsService
{
    protected $baseUrl = 'https://api.vagalume.com.br'; // Deprecated but kept for ref compat if needed

    public function search($query)
    {
        // Local Database Search Only (User Request to remove Vagalume)
        $localSongs = \Modules\Worship\App\Models\WorshipSong::where('title', 'like', "%{$query}%")
            ->orWhere('artist', 'like', "%{$query}%")
            ->take(20)
            ->get();

        $results = [];

        foreach ($localSongs as $song) {
            $results[] = [
                'id' => $song->id,
                'title' => $song->title,
                'artist' => $song->artist,
                'type' => 'local',
                'song_id' => $song->id // Explicit for frontend usage
            ];
        }

        return $results;
    }

    public function fetchLyrics($id)
    {
        // No external fetching anymore.
        // Frontend should handle local items directly.
        return null;
    }
}
