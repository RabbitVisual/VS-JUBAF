<?php

namespace Modules\Worship\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @deprecated Use AcademyCourse for all academy data (same table worship_academy_courses).
 *             RosterController and AcademyProgressController now use AcademyCourse.
 */
class WorshipAcademyCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'instrument_id',
        'level',
        'description',
        'cover_image',
    ];

    public function instrument()
    {
        return $this->belongsTo(WorshipInstrument::class);
    }

    public function lessons()
    {
        return $this->hasMany(WorshipAcademyLesson::class, 'course_id')->orderBy('order');
    }
}
