<?php

namespace Modules\PaymentGateway\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @deprecated Use PaymentGateway for all gateway configuration. This model is no longer used
 *             by PaymentService, DebugController or PaymentGatewayFactory. Table gateway_configs
 *             may be removed in a future migration.
 */
class GatewayConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver',
        'is_active',
        'settings',
        'certificate_path',
        'mode',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'encrypted:array',
    ];

    /**
     * Helper to check if sandbox mode.
     */
    public function isSandbox(): bool
    {
        return $this->mode === 'sandbox';
    }

    /**
     * Helper to check if production mode.
     */
    public function isProduction(): bool
    {
        return $this->mode === 'production';
    }
}
