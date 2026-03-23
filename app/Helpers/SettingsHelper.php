<?php

namespace App\Helpers;

use App\Models\Settings;

/**
 * Helper para aplicar configurações do banco de dados globalmente
 */
class SettingsHelper
{
    /**
     * Aplica todas as configurações do banco como variáveis de ambiente dinâmicas
     */
    public static function applyGlobalSettings(): void
    {
        // Broadcasting/Pusher
        $broadcastDriver = Settings::get('broadcast_driver');

        // Only override if not 'log' or 'null', or if config is empty
        if ($broadcastDriver && !in_array($broadcastDriver, ['log', 'null']) || !config('broadcasting.default')) {
            $effectiveDriver = $broadcastDriver;
            // Pusher requires credentials; avoid setting default to pusher when missing (e.g. during composer dump-autoload)
            if ($broadcastDriver === 'pusher') {
                $key = Settings::get('pusher_app_key') ?: config('broadcasting.connections.pusher.key');
                if (empty($key)) {
                    $effectiveDriver = config('broadcasting.default') ?: 'log';
                }
            }
            config(['broadcasting.default' => $effectiveDriver]);
        }

        $currentDriver = config('broadcasting.default');

        if ($currentDriver === 'pusher') {
            // Ensure we don't override with empty values if we have something in config/env
            $host = Settings::get('pusher_host');
            if ($host) {
                config(['broadcasting.connections.pusher.options.host' => $host]);
            }

            $port = Settings::get('pusher_port');
            if ($port) {
                config(['broadcasting.connections.pusher.options.port' => (int)$port]);
            }

            $scheme = Settings::get('pusher_scheme');
            if ($scheme) {
                config(['broadcasting.connections.pusher.options.scheme' => $scheme]);
                config(['broadcasting.connections.pusher.options.useTLS' => $scheme === 'https']);
            }

            $key = Settings::get('pusher_app_key');
            if ($key) {
                config(['broadcasting.connections.pusher.key' => $key]);
            }
        }

        // Mail
        if ($mailMailer = Settings::get('mail_mailer', config('mail.default'))) {
            config(['mail.default' => $mailMailer]);

            if ($mailMailer === 'smtp') {
                config([
                    'mail.mailers.smtp.host' => Settings::get('mail_host', config('mail.mailers.smtp.host')),
                    'mail.mailers.smtp.port' => Settings::get('mail_port', config('mail.mailers.smtp.port')),
                    'mail.mailers.smtp.username' => Settings::get('mail_username', config('mail.mailers.smtp.username')),
                    'mail.mailers.smtp.password' => Settings::get('mail_password', config('mail.mailers.smtp.password')),
                    'mail.mailers.smtp.encryption' => Settings::get('mail_encryption', config('mail.mailers.smtp.encryption')),
                ]);
            }

            config([
                'mail.from.address' => Settings::get('mail_from_address', Settings::get('site_email', config('mail.from.address'))),
                'mail.from.name' => Settings::get('mail_from_name', Settings::get('site_name', config('mail.from.name'))),
            ]);
        }

        // Cache
        if ($cacheStore = Settings::get('cache_store')) {
            config(['cache.default' => $cacheStore]);
        }

        // Session
        if ($sessionDriver = Settings::get('session_driver')) {
            config(['session.driver' => $sessionDriver]);
        }

        if ($sessionLifetime = Settings::get('session_lifetime')) {
            config(['session.lifetime' => $sessionLifetime]);
        }

        // Queue
        if ($queueConnection = Settings::get('queue_connection')) {
            config(['queue.default' => $queueConnection]);
        }

        // App Name
        if ($siteName = Settings::get('site_name')) {
            config(['app.name' => $siteName]);
        }

        // Regional: timezone & locale
        $appTimezone = Settings::get('app_timezone');
        if ($appTimezone) {
            config(['app.timezone' => $appTimezone]);
        }
        $appLocale = Settings::get('app_locale');
        if ($appLocale) {
            config(['app.locale' => $appLocale]);
        }
        $dateFormat = Settings::get('date_format');
        if ($dateFormat) {
            config(['app.date_format' => $dateFormat]);
        }
        $timeFormat = Settings::get('time_format');
        if ($timeFormat) {
            config(['app.time_format' => $timeFormat]);
        }

        // reCAPTCHA
        config([
            'services.recaptcha.enabled' => (bool) Settings::get('recaptcha_enabled', false),
            'services.recaptcha.version' => Settings::get('recaptcha_version', 'v2'),
            'services.recaptcha.v3_score_threshold' => (float) Settings::get('recaptcha_v3_score_threshold', 0.5),
            'services.recaptcha.site_key' => Settings::get('recaptcha_site_key', ''),
            'services.recaptcha.secret_key' => Settings::get('recaptcha_secret_key', ''),
        ]);

        // 2FA (preparação para futuro)
        config([
            'auth.2fa.enabled' => (bool) Settings::get('two_factor_enabled', false),
            'auth.2fa.provider' => Settings::get('two_factor_provider', 'none'),
        ]);

        // Amazon SES
        config([
            'services.ses.key' => Settings::get('ses_key', ''),
            'services.ses.secret' => Settings::get('ses_secret', ''),
            'services.ses.region' => Settings::get('ses_region', 'us-east-1'),
        ]);

        // Mailgun
        config([
            'services.mailgun.domain' => Settings::get('mailgun_domain', ''),
            'services.mailgun.secret' => Settings::get('mailgun_secret', ''),
            'services.mailgun.endpoint' => Settings::get('mailgun_endpoint', 'api.mailgun.net'),
        ]);
    }

    /**
     * Obtém configuração do Pusher para JavaScript
     */
    public static function getPusherConfig(): array
    {
        $defaultConnection = config('broadcasting.default');
        $config = config("broadcasting.connections.{$defaultConnection}");

        return [
            'key' => Settings::get('pusher_app_key', $config['key'] ?? ''),
            'cluster' => Settings::get('pusher_app_cluster', $config['options']['cluster'] ?? 'mt1'),
            'host' => Settings::get('pusher_host', $config['options']['host'] ?? ''),
            'port' => Settings::get('pusher_port', $config['options']['port'] ?? 443),
            'scheme' => Settings::get('pusher_scheme', $config['options']['scheme'] ?? 'https'),
        ];
    }
}
