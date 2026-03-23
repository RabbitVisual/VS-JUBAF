<?php

namespace Modules\Events\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventRegistration extends Model
{
    use SoftDeletes;

    protected $table = 'event_registrations';

    protected $fillable = [
        'uuid',
        'event_id',
        'batch_id',
        'user_id',
        'total_amount',
        'status',
        'payment_method',
        'payment_gateway_id',
        'payment_reference',
        'ticket_hash',
        'checked_in_at',
        'paid_at',
        'notes',
        'discount_code',
        'custom_responses',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'custom_responses' => 'array',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    /**
     * Get the event
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the batch (lote)
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(EventBatch::class, 'batch_id');
    }

    /**
     * Get participants
     */
    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class, 'registration_id');
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_CONFIRMED => 'Confirmada',
            self::STATUS_CANCELLED => 'Cancelada',
            self::STATUS_REFUNDED => 'Reembolsada',
            default => 'Pendente'
        };
    }

    /**
     * Scope for confirmed registrations
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    /**
     * Scope for pending registrations
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Get all payments
     */
    public function payments()
    {
        return $this->morphMany(\Modules\PaymentGateway\App\Models\Payment::class, 'payable');
    }

    /**
     * Get the latest payment
     */
    public function latestPayment()
    {
        return $this->morphOne(\Modules\PaymentGateway\App\Models\Payment::class, 'payable')->latestOfMany();
    }
}
