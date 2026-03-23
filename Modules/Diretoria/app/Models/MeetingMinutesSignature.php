<?php

namespace Modules\Diretoria\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingMinutesSignature extends Model
{
    protected $fillable = [
        'minutes_version_id',
        'user_id',
        'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function minutesVersion(): BelongsTo
    {
        return $this->belongsTo(MeetingMinutesVersion::class, 'minutes_version_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
