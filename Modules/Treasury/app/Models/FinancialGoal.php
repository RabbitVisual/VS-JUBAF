<?php

namespace Modules\Treasury\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialGoal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'icon',
        'color',
        'description',
        'type',
        'target_amount',
        'current_amount',
        'start_date',
        'end_date',
        'category',
        'campaign_id',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Relacionamento com campanha
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Relacionamento com entradas financeiras vinculadas diretamente
     */
    public function financialEntries()
    {
        return $this->hasMany(FinancialEntry::class, 'goal_id');
    }

    /**
     * Calcula o progresso da meta em porcentagem
     */
    public function getProgressPercentageAttribute(): float
    {
        if (! $this->target_amount || $this->target_amount == 0) {
            return 0;
        }

        return min(100, ($this->current_amount / $this->target_amount) * 100);
    }

    /**
     * Verifica se a meta foi alcançada
     */
    public function isAchieved(): bool
    {
        return $this->current_amount >= $this->target_amount;
    }

    /**
     * Atualiza o valor atual da meta baseado nas entradas financeiras
     */
    public function updateCurrentAmount(): void
    {
        // 1. Entradas vinculadas diretamente pelo goal_id
        $directSum = FinancialEntry::where('goal_id', $this->id)
            ->where('type', 'income')
            ->sum('amount');

        // 2. Entradas que correspondem aos critérios da meta (se aplicável)
        $query = FinancialEntry::where('type', 'income')
            ->whereBetween('entry_date', [$this->start_date, $this->end_date])
            ->whereNull('goal_id'); // Evitar duplicidade com as vinculadas diretamente

        if ($this->category) {
            $query->where('category', $this->category);
        }

        if ($this->campaign_id) {
            $query->where('campaign_id', $this->campaign_id);
        }

        // Se não houver categoria nem campanha, e não houver vínculos diretos,
        // talvez não deva somar tudo automaticamente se for uma meta customizada?
        // Mas o comportamento padrão atual é somar por período/categoria/campanha.

        $criteriaSum = $query->sum('amount');

        $this->current_amount = $directSum + $criteriaSum;
        $this->save();
    }

    /**
     * Scope para metas ativas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }
}
