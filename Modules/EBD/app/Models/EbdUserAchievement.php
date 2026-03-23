<?php

namespace Modules\EBD\App\Models;

use Illuminate\Database\Eloquent\Model;

class EbdUserAchievement extends Model
{
    protected $table = 'ebd_user_achievements';

    protected $fillable = [
        'user_id',
        'achievement_id',
        'awarded_at',
    ];

    protected $casts = [
        'awarded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function achievement()
    {
        return $this->belongsTo(EbdAchievement::class);
    }
}
