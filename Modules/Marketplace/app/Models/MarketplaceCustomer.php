<?php

namespace Modules\Marketplace\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class MarketplaceCustomer extends Authenticatable
{
    use Notifiable;

    protected $table = 'marketplace_customers';

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'phone',
        'document',
        'address_default',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'address_default' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (MarketplaceCustomer $customer): void {
            if (empty($customer->uuid)) {
                $customer->uuid = (string) Str::uuid();
            }
        });
    }

    public function setPasswordAttribute(string $value): void
    {
        if ($value === '' || Hash::needsRehash($value)) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'marketplace_customer_id');
    }
}

