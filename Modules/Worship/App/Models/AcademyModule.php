<?php

namespace Modules\Worship\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AcademyModule extends Model
{
    use HasFactory;

    protected $table = 'worship_academy_modules';

    protected $fillable = [
        'course_id',
        'title',
        'order',
    ];

    public function course()
    {
        return $this->belongsTo(AcademyCourse::class, 'course_id');
    }

    public function lessons()
    {
        return $this->hasMany(AcademyLesson::class, 'module_id')->orderBy('order');
    }
}
