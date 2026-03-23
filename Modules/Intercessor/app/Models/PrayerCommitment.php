<?php

namespace Modules\Intercessor\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrayerCommitment extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'user_id',
        'duration_seconds',
    ];

    public function request()
    {
        return $this->belongsTo(PrayerRequest::class, 'request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
