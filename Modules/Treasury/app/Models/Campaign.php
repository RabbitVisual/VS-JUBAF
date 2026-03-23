<?php

namespace Modules\Treasury\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Campaign extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'target_amount',
        'current_amount',
        'start_date',
        'end_date',
        'is_active',
        'image',
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
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($campaign) {
            if (empty($campaign->slug)) {
                $campaign->slug = Str::slug($campaign->name);
            }
        });
    }

    /**
     * Relacionamento com entradas financeiras
     */
    public function financialEntries(): HasMany
    {
        return $this->hasMany(FinancialEntry::class);
    }

    /**
     * Relacionamento com metas
     */
    public function goals(): HasMany
    {
        return $this->hasMany(FinancialGoal::class);
    }

    /**
     * Calcula o progresso da campanha em porcentagem
     */
    public function getProgressPercentageAttribute(): float
    {
        if (! $this->target_amount || $this->target_amount == 0) {
            return 0;
        }

        return min(100, ($this->current_amount / $this->target_amount) * 100);
    }

    /**
     * Verifica se a campanha está ativa
     */
    public function isActive(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    /**
     * Atualiza o valor atual da campanha
     */
    public function updateCurrentAmount(): void
    {
        $this->current_amount = $this->financialEntries()
            ->where('type', 'income')
            ->where('category', 'campaign')
            ->sum('amount');

        $this->save();
    }

    /**
     * Scope para campanhas ativas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }
}
