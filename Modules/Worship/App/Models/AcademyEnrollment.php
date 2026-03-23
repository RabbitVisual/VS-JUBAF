<?php

namespace Modules\Worship\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class AcademyEnrollment extends Model
{
    use HasFactory;

    protected $table = 'worship_academy_enrollments';

    protected $fillable = [
        'user_id',
        'course_id',
        'progress_percent',
        'completed_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(AcademyCourse::class);
    }
}
