<?php

namespace Modules\EBD\App\Models;

use Illuminate\Database\Eloquent\Model;

class EbdXpRule extends Model
{
    protected $table = 'ebd_xp_rules';

    protected $fillable = [
        'source_type',
        'source_slug',
        'formula',
        'value',
        'cap',
        'min',
        'is_active',
    ];

    protected $casts = [
        'value' => 'integer',
        'cap' => 'integer',
        'min' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Calculate XP for a game score (or fixed amount when formula=fixed).
     */
    public function calculateXp(int $score = 0): int
    {
        if (!$this->is_active) {
            return 0;
        }

        if ($this->formula === 'fixed') {
            $xp = $this->value;
        } else {
            // score_percent: value = percentage (e.g. 10 = 10%)
            $xp = (int) round($score * ($this->value / 100.0));
        }

        if ($this->cap !== null) {
            $xp = min($xp, $this->cap);
        }
        if ($score > 0 && $this->min > 0 && $xp < $this->min) {
            $xp = $this->min;
        }
        return max(0, $xp);
    }
}
