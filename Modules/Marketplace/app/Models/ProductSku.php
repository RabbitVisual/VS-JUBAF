<?php

namespace Modules\Marketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductSku extends Model
{
    protected $table = 'marketplace_product_skus';

    protected $fillable = [
        'product_id',
        'sku_code',
        'attributes',
        'price_override',
        'stock',
        'barcode',
        'weight_grams',
        'length_cm',
        'width_cm',
        'height_cm',
    ];

    protected $casts = [
        'attributes' => 'array',
        'price_override' => 'decimal:2',
        'stock' => 'integer',
        'weight_grams' => 'integer',
        'length_cm' => 'decimal:2',
        'width_cm' => 'decimal:2',
        'height_cm' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'sku_id');
    }

    /**
     * Display name for the variation (e.g. "Tamanho M, Cor Azul").
     */
    public function getDisplayNameAttribute(): string
    {
        if (empty($this->attributes) || ! is_array($this->attributes)) {
            return '';
        }
        $parts = [];
        foreach ($this->attributes as $name => $value) {
            $parts[] = "{$name} {$value}";
        }
        return implode(', ', $parts);
    }

    /**
     * Effective price: override or product price.
     */
    public function getEffectivePriceAttribute(): float
    {
        if ($this->price_override !== null) {
            return (float) $this->price_override;
        }
        return (float) $this->product->price;
    }
}
