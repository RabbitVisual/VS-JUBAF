<?php

namespace Modules\Bible\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BiblePlanDay extends Model
{
    protected $guarded = ['id'];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(BiblePlan::class, 'plan_id');
    }

    public function contents(): HasMany
    {
        return $this->hasMany(BiblePlanContent::class, 'plan_day_id')->orderBy('order_index');
    }
}
