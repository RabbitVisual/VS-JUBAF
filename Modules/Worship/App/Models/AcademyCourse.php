<?php

namespace Modules\Worship\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class AcademyCourse extends Model
{
    use HasFactory;

    protected $table = 'worship_academy_courses';

    protected $fillable = [
        'title',
        'slug',
        'instrument_id',
        'asset_id',
        'worship_team_role_id',
        'level',
        'difficulty_level',
        'category',
        'description',
        'biblical_reflection',
        'cover_image',
        'instructor_id',
        'status',
    ];

    protected $casts = [
        'category' => \Modules\Worship\App\Enums\AcademyCourseCategory::class,
    ];

    public function modules()
    {
        return $this->hasMany(AcademyModule::class, 'course_id')->orderBy('order');
    }

    public function lessons()
    {
        return $this->hasManyThrough(AcademyLesson::class, AcademyModule::class, 'course_id', 'module_id');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function enrollments()
    {
        return $this->hasMany(AcademyEnrollment::class, 'course_id');
    }

    public function instrument()
    {
        return $this->belongsTo(WorshipInstrument::class, 'instrument_id');
    }

    public function worshipTeamRole()
    {
        return $this->belongsTo(WorshipTeamRole::class, 'worship_team_role_id');
    }
}
