<?php

namespace Modules\Treasury\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialFund extends Model
{
    protected $table = 'financial_funds';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_restricted',
    ];

    protected $casts = [
        'is_restricted' => 'boolean',
    ];

    public function financialEntries(): HasMany
    {
        return $this->hasMany(FinancialEntry::class, 'fund_id');
    }
}
