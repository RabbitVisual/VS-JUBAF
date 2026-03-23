<?php

namespace Modules\Bible\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BiblePlan extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'allow_back_tracking' => 'boolean',
        'is_church_plan' => 'boolean',
    ];

    public function days(): HasMany
    {
        return $this->hasMany(BiblePlanDay::class, 'plan_id')->orderBy('day_number');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(BiblePlanSubscription::class, 'plan_id');
    }
}
