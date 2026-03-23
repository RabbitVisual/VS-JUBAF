<?php

namespace Modules\Worship\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AcademyLesson extends Model
{
    use HasFactory;

    protected $table = 'worship_academy_lessons';

    protected $fillable = [
        'module_id',
        'title',
        'slug',
        'type', // video, chordpro, material, devotional
        'content',
        'teacher_tips',
        'video_url',
        'multicam_video_url',
        'pdf_path',
        'sheet_music_pdf',
        'bible_reference',
        'requirement_song_id',
        'order',
        'duration_minutes',
    ];

    public function materials()
    {
        return $this->hasMany(WorshipLessonMaterial::class, 'lesson_id')->orderBy('order');
    }

    public function module()
    {
        return $this->belongsTo(AcademyModule::class, 'module_id');
    }

    public function progress()
    {
        return $this->hasMany(AcademyProgress::class, 'lesson_id');
    }
}
