<?php

namespace Modules\Events\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventBadge extends Model
{
    protected $fillable = [
        'event_id',
        'template_html',
        'orientation',
        'paper_size',
        'badges_per_page',
    ];

    protected $casts = [
        'badges_per_page' => 'integer',
    ];

    /**
     * Default badge template HTML
     */
    public static function getDefaultTemplate(): string
    {
        return <<<'HTML'
<div class="badge">
    <div class="badge-header">
        {{ evento }}
    </div>
    <div class="badge-body">
        <div class="participant-name">
            {{ nome }}
        </div>
        <div class="role-badge">
            {{ funcao }}
        </div>
    </div>
    <div class="badge-footer">
        {{ data }} • {{ local }}
    </div>
</div>
HTML;
    }

    /**
     * Get the event that owns the badge template.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
