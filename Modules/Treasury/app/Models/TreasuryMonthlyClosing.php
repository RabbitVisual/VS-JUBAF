<?php

namespace Modules\Treasury\App\Models;

use Illuminate\Database\Eloquent\Model;

class TreasuryMonthlyClosing extends Model
{
    protected $fillable = [
        'year',
        'month',
        'period_start',
        'period_end',
        'total_income',
        'total_expense',
        'balance',
        'ready_for_assembly',
        'council_approved_at',
        'council_approved_by',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'council_approved_at' => 'datetime',
        'ready_for_assembly' => 'boolean',
        'total_income' => 'float',
        'total_expense' => 'float',
        'balance' => 'float',
    ];

    public function scopeForPeriod($query, int $year, int $month)
    {
        return $query->where('year', $year)->where('month', $month);
    }

    public function scopeReadyForAssembly($query)
    {
        return $query->where('ready_for_assembly', true);
    }
}

