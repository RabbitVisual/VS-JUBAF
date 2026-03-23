<?php

namespace Modules\SocialAction\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Treasury\App\Models\Campaign as TreasuryCampaign;

class SocialCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'goal_description',
        'target_amount',
        'current_amount',
        'start_date',
        'end_date',
        'status',
        'treasury_campaign_id',
        'ministry_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
    ];

    /** Campanha da tesouraria vinculada (doações centralizadas). */
    public function treasuryCampaign()
    {
        return $this->belongsTo(TreasuryCampaign::class, 'treasury_campaign_id');
    }

    public function ministry()
    {
        return $this->belongsTo(\Modules\Ministries\App\Models\Ministry::class, 'ministry_id');
    }

    public function getProgressPercentageAttribute(): float
    {
        if (! $this->target_amount || (float) $this->target_amount === 0.0) {
            return 0;
        }

        return min(100, ((float) $this->current_amount / (float) $this->target_amount) * 100);
    }

    public function getStatusDisplayAttribute(): string
    {
        return self::getStatusDisplayName($this->status);
    }

    public static function getStatusDisplayName(?string $status): string
    {
        return match ($status) {
            'active' => 'Ativa',
            'completed' => 'Concluída',
            'cancelled' => 'Cancelada',
            'paused' => 'Pausada',
            default => $status ?? '-',
        };
    }
}
