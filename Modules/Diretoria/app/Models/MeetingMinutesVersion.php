<?php

namespace Modules\Diretoria\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeetingMinutesVersion extends Model
{
    protected $fillable = [
        'reuniao_id',
        'version',
        'content',
        'state',
        'created_by',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Reuniao::class, 'reuniao_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(MeetingMinutesSignature::class, 'minutes_version_id');
    }
}
