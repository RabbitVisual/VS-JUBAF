<?php

namespace Modules\EBD\App\Models;

use Illuminate\Database\Eloquent\Model;

class GamificationLevel extends Model
{
    protected $table = 'ebd_gamification_levels';

    protected $fillable = [
        'level_number',
        'name',
        'xp_required',
        'icon_path',
    ];

    public function getMinXpAttribute()
    {
        return $this->xp_required;
    }

    public function getBadgeIconAttribute()
    {
        return $this->icon_path;
    }

    public function setMinXpAttribute($value)
    {
        $this->attributes['xp_required'] = $value;
    }

    public function setBadgeIconAttribute($value)
    {
        $this->attributes['icon_path'] = $value;
    }
}
