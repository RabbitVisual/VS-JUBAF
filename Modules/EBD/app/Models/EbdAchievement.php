<?php

namespace Modules\EBD\App\Models;

use Illuminate\Database\Eloquent\Model;

class EbdAchievement extends Model
{
    protected $table = 'ebd_achievements';

    protected $fillable = [
        'slug',
        'name',
        'description',
        'icon_id',
        'icon_fa_name',
        'tier',
        'difficulty',
        'trigger_type',
        'trigger_value',
        'xp_bonus',
        'order',
        'is_active',
        'is_hidden',
    ];

    protected $casts = [
        'trigger_value' => 'array',
        'xp_bonus' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
        'is_hidden' => 'boolean',
    ];

    public function icon()
    {
        return $this->belongsTo(GlobalIcon::class, 'icon_id');
    }

    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class, 'ebd_user_achievements', 'achievement_id', 'user_id')
            ->withPivot('awarded_at')
            ->withTimestamps();
    }
}
