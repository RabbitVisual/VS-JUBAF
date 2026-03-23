<?php

namespace Modules\Assets\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetReservation extends Model
{
    public const STATUS_REQUESTED = 'requested';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_DENIED = 'denied';

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'asset_id',
        'ministry_id',
        'event_id',
        'requested_by',
        'start_at',
        'end_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(\Modules\Ministries\App\Models\Ministry::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(\Modules\Events\App\Models\Event::class, 'event_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function scopeRequested($query)
    {
        return $query->where('status', self::STATUS_REQUESTED);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeForMinistry($query, int $ministryId)
    {
        return $query->where('ministry_id', $ministryId);
    }

    public static function hasCollision(int $assetId, $startAt, $endAt): bool
    {
        return static::where('asset_id', $assetId)
            ->whereIn('status', [self::STATUS_REQUESTED, self::STATUS_APPROVED])
            ->where('start_at', '<', $endAt)
            ->where('end_at', '>', $startAt)
            ->exists();
    }
}
