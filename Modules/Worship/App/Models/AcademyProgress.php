<?php

namespace Modules\Worship\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class AcademyProgress extends Model
{
    use HasFactory;

    protected $table = 'worship_academy_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'completed_at',
        'score',
        'feedback',
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
        return $this->belongsTo(AcademyLesson::class);
    }
}
