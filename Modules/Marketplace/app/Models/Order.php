<?php

namespace Modules\Marketplace\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;
use Modules\PaymentGateway\App\Models\Payment;
use Modules\Treasury\App\Models\Campaign;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_SHIPPED_READY_FOR_PICKUP = 'shipped_ready_for_pickup';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $table = 'marketplace_orders';

    protected $fillable = [
        'uuid',
        'user_id',
        'marketplace_customer_id',
        'email',
        'payer_name',
        'status',
        'delivery_type',
        'shipping_address',
        'pickup_location_id',
        'tracking_code',
        'total_amount',
        'shipping_amount',
        'coupon_id',
        'discount_amount',
        'campaign_id',
        'paid_at',
        'shipped_at',
        'completed_at',
        'metadata',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_address' => 'array',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Order $order): void {
            if (empty($order->uuid)) {
                $order->uuid = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function marketplaceCustomer(): BelongsTo
    {
        return $this->belongsTo(MarketplaceCustomer::class, 'marketplace_customer_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function pickupLocation(): BelongsTo
    {
        return $this->belongsTo(PickupLocation::class, 'pickup_location_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function payment(): MorphOne
    {
        return $this->morphOne(Payment::class, 'payable');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => __('marketplace::messages.status_pending'),
            self::STATUS_PAID => __('marketplace::messages.status_paid'),
            self::STATUS_PREPARING => __('marketplace::messages.status_preparing'),
            self::STATUS_SHIPPED_READY_FOR_PICKUP => __('marketplace::messages.status_shipped_ready_for_pickup'),
            self::STATUS_COMPLETED => __('marketplace::messages.status_completed'),
            self::STATUS_CANCELLED => __('marketplace::messages.status_cancelled'),
        ];
    }

    public function getTrackingUrlAttribute(): ?string
    {
        if (empty($this->tracking_code)) {
            return null;
        }

        return 'https://rastreamento.correios.com.br/app/index.php?objeto=' . urlencode($this->tracking_code);
    }
}
