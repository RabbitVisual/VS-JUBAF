<?php

namespace Modules\Bible\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class BibleVersion extends Model
{
    protected $fillable = [
        'name',
        'abbreviation',
        'description',
        'language',
        'file_name',
        'is_active',
        'is_default',
        'total_books',
        'total_chapters',
        'total_verses',
        'imported_at',
        'audio_url_template',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'imported_at' => 'datetime',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function chapterAudios(): HasMany
    {
        return $this->hasMany(BibleChapterAudio::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function getTotalBooksAttribute($value)
    {
        if ($value !== null && $value > 0) {
            return $value;
        }

        return $this->books()->count();
    }

    public function getTotalChaptersAttribute($value)
    {
        if ($value !== null && $value > 0) {
            return $value;
        }

        return $this->books()->withCount('chapters')->get()->sum('chapters_count');
    }

    public function getTotalVersesAttribute($value)
    {
        if ($value !== null && $value > 0) {
            return $value;
        }

        return $this->books()->withCount('verses')->get()->sum('verses_count');
    }

    /**
     * Get chapter audio URL: first from bible_chapter_audio table (e.g. Google Drive per-file),
     * then from audio_url_template if set.
     * Drive "view" URLs are normalized to direct download for <audio src="">.
     *
     * @return string|null URL or null if none configured
     */
    public function getChapterAudioUrl(int $bookNumber, int $chapterNumber): ?string
    {
        if (Schema::hasTable('bible_chapter_audio')) {
            $row = $this->chapterAudios()
                ->where('book_number', $bookNumber)
                ->where('chapter_number', $chapterNumber)
                ->first();

            if ($row && ! empty($row->audio_url)) {
                return BibleChapterAudio::normalizeAudioUrl($row->audio_url);
            }
        }

        $template = $this->audio_url_template;
        if (empty($template) || ! is_string($template)) {
            return null;
        }

        return str_replace(
            ['{book_number}', '{chapter_number}'],
            [(string) $bookNumber, (string) $chapterNumber],
            $template
        );
    }
}
