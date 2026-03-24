<?php

namespace Modules\Treasury\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Ministries\App\Models\Ministry;
use Modules\PaymentGateway\App\Models\Payment;
use Modules\Igrejas\Models\Igreja;
class FinancialEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'category',
        'category_id',
        'title',
        'description',
        'amount',
        'entry_date',
        'user_id',
        'member_id',
        'payment_id',
        'campaign_id',
        'goal_id',
        'ministry_id',
        'fund_id',
        'reversal_of_id',
        'payment_method',
        'reference_number',
        'metadata',
        'diretoria_approval_id',
        'diretoria_approved_at',
        'expense_status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'entry_date' => 'date',
        'metadata' => 'array',
        'diretoria_approved_at' => 'datetime',
    ];

    public const EXPENSE_STATUS_PENDING = 'pending';

    public const EXPENSE_STATUS_APPROVED = 'approved';

    public const EXPENSE_STATUS_PAID = 'paid';

    /**
     * Relacionamento com usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com pagamento (PaymentGateway)
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Relacionamento com campanha
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Relacionamento com meta financeira
     */
    public function goal(): BelongsTo
    {
        return $this->belongsTo(FinancialGoal::class);
    }

    /**
     * Relacionamento com ministério
     */
    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    /**
     * Aprovação do conselho (quando despesa acima do limite).
     */
    public function DiretoriaApproval(): BelongsTo
    {
        return $this->belongsTo(\Modules\Diretoria\App\Models\DiretoriaApproval::class, 'diretoria_approval_id');
    }

    public function financialCategory(): BelongsTo
    {
        return $this->belongsTo(FinancialCategory::class, 'category_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function fund(): BelongsTo
    {
        return $this->belongsTo(FinancialFund::class, 'fund_id');
    }

    public function reversalOf(): BelongsTo
    {
        return $this->belongsTo(FinancialEntry::class, 'reversal_of_id');
    }

    /**
     * Obter a Igreja vinculada ao lançamento (via metadata).
     */
    public function getIgrejaAttribute(): ?Igreja
    {
        $igrejaId = $this->metadata['igreja_id'] ?? null;
        return $igrejaId ? Igreja::find($igrejaId) : null;
    }

    public function getIgrejaIdAttribute()
    {
        return $this->metadata['igreja_id'] ?? null;
    }

    /**
     * Scope para entradas (receitas)
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope para saídas (despesas)
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Scope por categoria
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope por período
     */
    public function scopePeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('entry_date', [$startDate, $endDate]);
    }

    /**
     * Scope por mês
     */
    public function scopeMonth($query, $year, $month)
    {
        return $query->whereYear('entry_date', $year)
            ->whereMonth('entry_date', $month);
    }

    /**
     * Scope por ano
     */
    public function scopeYear($query, $year)
    {
        return $query->whereYear('entry_date', $year);
    }
}
