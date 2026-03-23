<?php

namespace Modules\Bible\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BibleChapterAudio extends Model
{
    protected $table = 'bible_chapter_audio';

    protected $fillable = [
        'bible_version_id',
        'book_number',
        'chapter_number',
        'audio_url',
    ];

    protected $casts = [
        'book_number' => 'integer',
        'chapter_number' => 'integer',
    ];

    public function bibleVersion(): BelongsTo
    {
        return $this->belongsTo(BibleVersion::class);
    }

    /**
     * Normalize Google Drive "view" URL to direct download URL for use in <audio src="">.
     * Example: https://drive.google.com/file/d/FILE_ID/view -> https://drive.google.com/uc?export=download&id=FILE_ID
     */
    public static function normalizeAudioUrl(?string $url): ?string
    {
        if ($url === null || $url === '') {
            return null;
        }
        $url = trim($url);
        if (preg_match('#drive\.google\.com/file/d/([a-zA-Z0-9_-]+)/#', $url, $m)) {
            return 'https://drive.google.com/uc?export=download&id='.$m[1];
        }
        if (preg_match('#drive\.google\.com/open\?id=([a-zA-Z0-9_-]+)#', $url, $m)) {
            return 'https://drive.google.com/uc?export=download&id='.$m[1];
        }

        return $url;
    }
}
