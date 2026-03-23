<?php

namespace Modules\PaymentGateway\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Modules\PaymentGateway\App\Models\PaymentAuditLog;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'payment_gateway_id',
        'payment_type',
        'payable_type',
        'payable_id',
        'transaction_id',
        'gateway_transaction_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'gateway_response',
        'metadata',
        'description',
        'payer_name',
        'payer_email',
        'payer_document',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'string',
        'gateway_response' => 'array',
        'metadata' => 'array',
        'paid_at' => 'datetime',
    ];

    /**
     * Relacionamento com usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com gateway
     */
    public function gateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_gateway_id');
    }

    /**
     * Relacionamento polimórfico (Ministry, etc)
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relacionamento com entrada financeira (Treasury)
     */
    public function financialEntry(): HasOne
    {
        return $this->hasOne(\Modules\Treasury\App\Models\FinancialEntry::class, 'payment_id');
    }

    /**
     * Log de auditoria de alterações de status
     */
    public function auditLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PaymentAuditLog::class)->orderByDesc('created_at');
    }

    /**
     * Scope para pagamentos pendentes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope para pagamentos completos
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope para pagamentos por tipo
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('payment_type', $type);
    }

    /**
     * Verifica se o pagamento está completo
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Verifica se o pagamento está pendente
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Verifica se o pagamento falhou
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Formata o valor para exibição
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'R$ '.number_format($this->amount, 2, ',', '.');
    }
}
