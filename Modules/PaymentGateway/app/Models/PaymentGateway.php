<?php

namespace Modules\PaymentGateway\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class PaymentGateway extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'icon',
        'is_active',
        'is_test_mode',
        'credentials',
        'settings',
        'supported_methods',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_test_mode' => 'boolean',
        'credentials' => 'array',
        'settings' => 'array',
        'supported_methods' => 'array',
        'sort_order' => 'integer',
    ];

    /**
     * Get the display name or fall back to formatted name.
     */
    public function getDisplayNameAttribute($value)
    {
        if (! empty($value)) {
            return $value;
        }

        return ucwords(str_replace(['_', '-'], ' ', $this->name));
    }

    /**
     * Get the Full Logo URL
     */
    public function getLogoUrlAttribute()
    {
        // 1. Is it a custom uploaded file? (Check if string contains 'gateways/')
        if (! empty($this->icon) && str_contains($this->icon, 'gateways/')) {
            return asset('storage/'.$this->icon);
        }

        // 2. Official Fallbacks
        if ($this->name === 'mercado_pago') {
            return asset('storage/image/mercadopago/logo.png');
        }
        if ($this->name === 'stripe') {
            return asset('storage/image/stripe/logo.png');
        }

        // 3. Fallback to generic icon path (if used purely as image source)
        // or return null to let view decide to render a component instead.
        return null;
    }

    /**
     * Get the supported methods based on gateway name or DB config.
     */
    public function getSupportedMethodsAttribute($value)
    {
        // If the DB column is a string (e.g., failed cast or raw access), decode it
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            }
        }

        // If the DB value is explicitly set (including an empty array), use it.
        // We only use the fallback if the value is null.
        if ($value !== null) {
            return (array) $value;
        }

        // Default supported methods based on gateway name (Fallback)
        switch ($this->name) {
            case 'mercado_pago':
                return ['pix', 'credit_card', 'boleto'];
            case 'stripe':
                return ['credit_card'];
            default:
                return [];
        }
    }

    /**
     * Relacionamento com pagamentos
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Obtém credenciais descriptografadas
     */
    public function getDecryptedCredentials(): array
    {
        if (empty($this->credentials)) {
            return [];
        }

        $decrypted = [];
        foreach ($this->credentials as $key => $value) {
            if (empty($value)) {
                $decrypted[$key] = null;

                continue;
            }

            try {
                $decrypted[$key] = Crypt::decryptString($value);
            } catch (\Exception $e) {
                // Se falhar a descriptografia, verificamos se o valor parece ser um JSON (payload do Laravel)
                // Se for um payload que não conseguimos descriptografar (ex: chave APP_KEY diferente),
                // retornamos null para não vazar a string criptografada para o frontend.
                $decoded = json_decode(base64_decode($value), true);
                if ($decoded && isset($decoded['iv'], $decoded['value'], $decoded['mac'])) {
                    $decrypted[$key] = null;
                } else {
                    $decrypted[$key] = $value; // Provavelmente texto puro
                }
            }
        }

        return $decrypted;
    }

    /**
     * Define credenciais criptografadas
     */
    public function setEncryptedCredentials(array $credentials): void
    {
        $encrypted = [];
        foreach ($credentials as $key => $value) {
            if (! empty($value)) {
                $encrypted[$key] = Crypt::encryptString($value);
            }
        }
        $this->credentials = $encrypted;
    }

    /**
     * Scope para gateways ativos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para ordenar
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('display_name');
    }

    /**
     * Verifica se o gateway está configurado
     */
    public function isConfigured(): bool
    {
        $credentials = $this->getDecryptedCredentials();

        switch ($this->name) {
            case 'stripe':
                return ! empty($credentials['public_key']) && ! empty($credentials['secret_key']);
            case 'mercado_pago':
                return ! empty($credentials['public_key']) && ! empty($credentials['access_token']);
            default:
                return false;
        }
    }
}
