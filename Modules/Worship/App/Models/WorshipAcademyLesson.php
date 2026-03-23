<?php

namespace Modules\Worship\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @deprecated Use AcademyLesson for all academy lessons (same table worship_academy_lessons).
 */
class WorshipAcademyLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'slug',
        'video_url',
        'pdf_path',
        'requirement_song_id',
        'order',
        'duration_minutes',
    ];

    public function course()
    {
        return $this->belongsTo(WorshipAcademyCourse::class, 'course_id');
    }

    public function song()
    {
        return $this->belongsTo(WorshipSong::class, 'requirement_song_id');
    }

    public function progress()
    {
        return $this->hasMany(WorshipMusicianProgress::class, 'lesson_id');
    }
}
