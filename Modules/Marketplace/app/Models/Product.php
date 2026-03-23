<?php

namespace Modules\Marketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Modules\Treasury\App\Models\Campaign;

class Product extends Model
{
    use SoftDeletes;

    public const CATEGORY_ALIMENTACAO = 'alimentacao';
    public const CATEGORY_VESTUARIO = 'vestuario';
    public const CATEGORY_LIVROS = 'livros';
    public const CATEGORY_EVENTOS_OFICINAS = 'eventos_oficinas';

    public const DELIVERY_LOCAL_PICKUP = 'local_pickup';
    public const DELIVERY_SHIPPING = 'shipping';
    public const DELIVERY_BOTH = 'both';

    protected $table = 'marketplace_products';

    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'description',
        'image_url',
        'sample_url',
        'video_url',
        'video_path',
        'specifications',
        'price',
        'compare_at_price',
        'stock',
        'category',
        'campaign_id',
        'pickup_location_id',
        'delivery_type',
        'weight_grams',
        'length_cm',
        'width_cm',
        'height_cm',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'specifications' => 'array',
        'stock' => 'integer',
        'weight_grams' => 'integer',
        'length_cm' => 'decimal:2',
        'width_cm' => 'decimal:2',
        'height_cm' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Product $product): void {
            if (empty($product->uuid)) {
                $product->uuid = (string) Str::uuid();
            }
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->title);
            }
        });
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function pickupLocation(): BelongsTo
    {
        return $this->belongsTo(PickupLocation::class, 'pickup_location_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class)->orderBy('sort_order');
    }

    public function skus(): HasMany
    {
        return $this->hasMany(ProductSku::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function hasVariations(): bool
    {
        return $this->skus()->exists();
    }

    public function isNew(int $days = 30): bool
    {
        return $this->created_at && $this->created_at->gte(now()->subDays($days));
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if ($this->compare_at_price === null || (float) $this->compare_at_price <= 0 || (float) $this->compare_at_price <= (float) $this->price) {
            return null;
        }
        return (int) round((1 - (float) $this->price / (float) $this->compare_at_price) * 100);
    }

    public static function categories(): array
    {
        return [
            self::CATEGORY_ALIMENTACAO => __('marketplace::messages.category_alimentacao'),
            self::CATEGORY_VESTUARIO => __('marketplace::messages.category_vestuario'),
            self::CATEGORY_LIVROS => __('marketplace::messages.category_livros'),
            self::CATEGORY_EVENTOS_OFICINAS => __('marketplace::messages.category_eventos_oficinas'),
        ];
    }

    public static function deliveryTypes(): array
    {
        return [
            self::DELIVERY_LOCAL_PICKUP => __('marketplace::messages.delivery_local_pickup'),
            self::DELIVERY_SHIPPING => __('marketplace::messages.delivery_shipping'),
            self::DELIVERY_BOTH => __('marketplace::messages.delivery_both'),
        ];
    }
}
