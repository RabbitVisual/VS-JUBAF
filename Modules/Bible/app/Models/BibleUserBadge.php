<?php

namespace Modules\Bible\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BibleUserBadge extends Model
{
    protected $table = 'bible_user_badges';

    protected $guarded = ['id'];

    protected $casts = [
        'awarded_at' => 'datetime',
    ];

    public const BADGE_BEREANO_SEMANA = 'bereano_semana';

    public const BADGE_FIEL_AO_PACTO = 'fiel_ao_pacto';

    public const BADGE_LEITOR_DO_CORPO = 'leitor_do_corpo';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(BiblePlanSubscription::class, 'subscription_id');
    }
}
