<?php

namespace App\Services;

use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthService
{
    public function __construct(
        protected Google2FA $google2fa
    ) {}

    /**
     * Gera uma nova secret TOTP para o usuário (não persiste).
     */
    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey(32);
    }

    /**
     * Retorna a URL otpauth para gerar o QR Code (Google/Microsoft Authenticator).
     */
    public function getQrCodeUrl(string $email, string $secret, string $issuer = 'Vertex CBAV'): string
    {
        return $this->google2fa->getQRCodeUrl($issuer, $email, $secret);
    }

    /**
     * Verifica se o código de 6 dígitos é válido para a secret dada.
     */
    public function verify(string $secret, string $code): bool
    {
        if (strlen($code) !== 6 || ! ctype_digit($code)) {
            return false;
        }

        return $this->google2fa->verifyKey($secret, $code);
    }

    /**
     * Ativa 2FA para o usuário: persiste a secret e marca como confirmado.
     */
    public function enableForUser(User $user, string $secret): void
    {
        $user->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ])->save();
    }

    /**
     * Desativa 2FA para o usuário.
     */
    public function disableForUser(User $user): void
    {
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
    }
}
