<?php

namespace Modules\HomePage\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsletterSubscriber extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'email',
        'name',
        'is_active',
        'subscribed_at',
        'unsubscribed_at',
        'confirmation_token',
        'is_confirmed',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_confirmed' => 'boolean',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    /**
     * Scope para assinantes ativos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_confirmed', true);
    }

    /**
     * Scope para assinantes confirmados
     */
    public function scopeConfirmed($query)
    {
        return $query->where('is_confirmed', true);
    }

    /**
     * Verificar se o e-mail já está cadastrado
     */
    public static function isEmailSubscribed($email)
    {
        return static::where('email', $email)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Gerar token de confirmação
     */
    public static function generateConfirmationToken()
    {
        return bin2hex(random_bytes(32));
    }
}
