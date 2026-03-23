<?php

namespace Modules\Worship\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @deprecated Use AcademyProgress (table worship_academy_progress). Legacy v1 progress table was renamed in v2.
 */
class WorshipMusicianProgress extends Model
{
    use HasFactory;

    protected $table = 'worship_academy_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'completed_at',
        'score',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(WorshipAcademyLesson::class, 'lesson_id');
    }
}
