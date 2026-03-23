<?php

namespace Modules\Admin\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para todas as abas de Configurações Globais.
     */
    public function rules(): array
    {
        return [
            // Geral
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'site_email' => 'required|email|max:255',
            'site_phone' => 'nullable|string|max:20',
            'site_address' => 'nullable|string|max:500',
            'logo' => 'nullable|image|max:2048',
            'logo_icon' => 'nullable|image|max:2048',

            // Pusher/Broadcasting
            'broadcast_driver' => 'nullable|in:log,pusher,redis',
            'pusher_app_id' => 'nullable|string|max:255',
            'pusher_app_key' => 'nullable|string|max:255',
            'pusher_app_secret' => 'nullable|string|max:255',
            'pusher_app_cluster' => 'nullable|string|max:50',
            'pusher_host' => 'nullable|string|max:255',
            'pusher_port' => 'nullable|integer|min:1|max:65535',
            'pusher_scheme' => 'nullable|in:http,https',

            // E-mail
            'mail_mailer' => 'nullable|in:smtp,log,array,ses,postmark,mailgun,sendmail',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|in:tls,ssl,null',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',

            // Cache e Sessão
            'cache_store' => 'nullable|in:array,database,file,memcached,redis',
            'session_driver' => 'nullable|in:file,cookie,database,memcached,redis,array',
            'session_lifetime' => 'nullable|integer|min:1|max:525600',

            // Fila
            'queue_connection' => 'nullable|in:sync,database,beanstalkd,sqs,redis',

            // Segurança / reCAPTCHA
            'recaptcha_enabled' => 'boolean',
            'recaptcha_version' => 'nullable|in:v2,v3',
            'recaptcha_v3_score_threshold' => 'nullable|numeric|min:0|max:1',
            'recaptcha_site_key' => 'nullable|string|max:255',
            'recaptcha_secret_key' => 'nullable|string|max:255',

            // 2FA TOTP
            'two_factor_enabled' => 'boolean',
            'two_factor_provider' => 'nullable|in:none,google,microsoft',

            // Regional
            'app_timezone' => 'nullable|string|max:50|timezone',
            'app_locale' => 'nullable|string|max:10|in:pt_BR,en,es',
            'app_first_day_of_week' => 'nullable|in:0,1',
            'date_format' => 'nullable|string|max:20',
            'time_format' => 'nullable|string|max:20',

            // Bíblia
            'default_bible_version_abbreviation' => 'nullable|string|max:20',

            // SES
            'ses_key' => 'nullable|string|max:255',
            'ses_secret' => 'nullable|string|max:255',
            'ses_region' => 'nullable|string|max:50',

            // Mailgun
            'mailgun_domain' => 'nullable|string|max:255',
            'mailgun_secret' => 'nullable|string|max:255',
            'mailgun_endpoint' => 'nullable|string|max:255',
        ];
    }
}
