<?php

namespace Modules\Treasury\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialCategory extends Model
{
    protected $table = 'financial_categories';

    protected $fillable = [
        'type',
        'slug',
        'name',
        'description',
        'is_system',
        'order',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'order' => 'integer',
    ];

    public function financialEntries(): HasMany
    {
        return $this->hasMany(FinancialEntry::class, 'category_id');
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }
}
