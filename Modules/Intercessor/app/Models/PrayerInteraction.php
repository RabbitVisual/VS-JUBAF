<?php

namespace Modules\Intercessor\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrayerInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'user_id',
        'parent_id',
        'type', // comment, prayer_log, testimony
        'body',
        'bible_reference_id',
    ];

    public function parent()
    {
        return $this->belongsTo(PrayerInteraction::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(PrayerInteraction::class, 'parent_id');
    }

    public function request()
    {
        return $this->belongsTo(PrayerRequest::class, 'request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
