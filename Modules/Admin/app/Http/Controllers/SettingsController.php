<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\SettingsHelper;
use App\Models\Settings;
use App\Services\MaintenanceModeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Admin\App\Http\Requests\UpdateSettingsRequest;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $bibleVersions = [];
        if (class_exists(\Modules\Bible\App\Models\BibleVersion::class)) {
            $bibleVersions = \Modules\Bible\App\Models\BibleVersion::active()->orderBy('name')->get();
        }

        return view('admin::settings.index', compact('bibleVersions'));
    }

    /**
     * Atualiza as configurações globais. Validação em UpdateSettingsRequest;
     * controller apenas orquestra Settings::set(), cache buster e redirect.
     */
    public function update(UpdateSettingsRequest $request)
    {
        $validated = $request->validated();

        // General Settings
        Settings::set('site_name', $validated['site_name'], 'string', 'general', 'Nome do site');
        Settings::set('site_description', $validated['site_description'] ?? '', 'text', 'general', 'Descrição do site');
        Settings::set('site_email', $validated['site_email'], 'string', 'general', 'E-mail de contato');
        Settings::set('site_phone', $validated['site_phone'] ?? '', 'string', 'general', 'Telefone de contato');
        Settings::set('site_address', $validated['site_address'] ?? '', 'text', 'general', 'Endereço da igreja');

        // Appearance
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('image', 'public');
            Settings::set('logo_path', 'storage/'.$logoPath, 'file', 'appearance', 'Logo oficial do site');
        }

        if ($request->hasFile('logo_icon')) {
            $iconPath = $request->file('logo_icon')->store('image', 'public');
            Settings::set('logo_icon_path', 'storage/'.$iconPath, 'file', 'appearance', 'Ícone/Favicon do site');
        }

        // Pusher/Broadcasting Settings
        Settings::set('broadcast_driver', $validated['broadcast_driver'] ?? 'log', 'string', 'broadcasting', 'Driver de broadcasting');
        Settings::set('pusher_app_id', $validated['pusher_app_id'] ?? '', 'string', 'broadcasting', 'Pusher App ID');
        Settings::set('pusher_app_key', $validated['pusher_app_key'] ?? '', 'string', 'broadcasting', 'Pusher App Key');
        Settings::set('pusher_app_secret', $validated['pusher_app_secret'] ?? '', 'string', 'broadcasting', 'Pusher App Secret');
        Settings::set('pusher_app_cluster', $validated['pusher_app_cluster'] ?? 'mt1', 'string', 'broadcasting', 'Pusher Cluster');
        Settings::set('pusher_host', $validated['pusher_host'] ?? '', 'string', 'broadcasting', 'Pusher Host');
        Settings::set('pusher_port', $validated['pusher_port'] ?? 443, 'integer', 'broadcasting', 'Pusher Port');
        Settings::set('pusher_scheme', $validated['pusher_scheme'] ?? 'https', 'string', 'broadcasting', 'Pusher Scheme');

        // Email Settings
        Settings::set('mail_mailer', $validated['mail_mailer'] ?? 'log', 'string', 'email', 'Mailer de e-mail');
        Settings::set('mail_host', $validated['mail_host'] ?? '', 'string', 'email', 'Host SMTP');
        Settings::set('mail_port', $validated['mail_port'] ?? 587, 'integer', 'email', 'Porta SMTP');
        Settings::set('mail_username', $validated['mail_username'] ?? '', 'string', 'email', 'Usuário SMTP');
        Settings::set('mail_password', $validated['mail_password'] ?? '', 'string', 'email', 'Senha SMTP');
        Settings::set('mail_encryption', $validated['mail_encryption'] ?? 'tls', 'string', 'email', 'Criptografia SMTP');
        Settings::set('mail_from_address', $validated['mail_from_address'] ?? $validated['site_email'], 'string', 'email', 'E-mail remetente');
        Settings::set('mail_from_name', $validated['mail_from_name'] ?? $validated['site_name'], 'string', 'email', 'Nome remetente');

        // Cache & Session Settings
        Settings::set('cache_store', $validated['cache_store'] ?? 'database', 'string', 'system', 'Driver de cache');
        Settings::set('session_driver', $validated['session_driver'] ?? 'database', 'string', 'system', 'Driver de sessão');
        Settings::set('session_lifetime', $validated['session_lifetime'] ?? 120, 'integer', 'system', 'Tempo de vida da sessão (minutos)');

        // Queue Settings
        Settings::set('queue_connection', $validated['queue_connection'] ?? 'database', 'string', 'system', 'Conexão de fila');

        // Security / reCAPTCHA Settings
        Settings::set('recaptcha_enabled', $validated['recaptcha_enabled'] ?? false, 'boolean', 'security', 'Habilitar reCAPTCHA no login');
        Settings::set('recaptcha_version', $validated['recaptcha_version'] ?? 'v2', 'string', 'security', 'Versão reCAPTCHA (v2 ou v3)');
        Settings::set('recaptcha_v3_score_threshold', $validated['recaptcha_v3_score_threshold'] ?? 0.5, 'float', 'security', 'Score mínimo reCAPTCHA v3 (0-1)');
        Settings::set('recaptcha_site_key', $validated['recaptcha_site_key'] ?? '', 'string', 'security', 'reCAPTCHA Site Key');
        Settings::set('recaptcha_secret_key', $validated['recaptcha_secret_key'] ?? '', 'string', 'security', 'reCAPTCHA Secret Key');

        // 2FA (preparação)
        Settings::set('two_factor_enabled', $validated['two_factor_enabled'] ?? false, 'boolean', 'security', 'Habilitar 2FA para administradores');
        Settings::set('two_factor_provider', $validated['two_factor_provider'] ?? 'none', 'string', 'security', 'Provedor 2FA TOTP (google/microsoft)');

        // Regional
        Settings::set('app_timezone', $validated['app_timezone'] ?? config('app.timezone'), 'string', 'general', 'Fuso horário padrão');
        Settings::set('app_locale', $validated['app_locale'] ?? config('app.locale'), 'string', 'general', 'Idioma padrão');
        Settings::set('app_first_day_of_week', $validated['app_first_day_of_week'] ?? '0', 'string', 'general', 'Primeiro dia da semana (0=Dom, 1=Seg)');
        Settings::set('date_format', $validated['date_format'] ?? 'd/m/Y', 'string', 'general', 'Formato de data');
        Settings::set('time_format', $validated['time_format'] ?? 'H:i', 'string', 'general', 'Formato de hora');

        // Bible
        Settings::set('default_bible_version_abbreviation', $validated['default_bible_version_abbreviation'] ?? '', 'string', 'bible', 'Versão padrão da Bíblia (sigla)');

        // SES Settings
        Settings::set('ses_key', $validated['ses_key'] ?? '', 'string', 'email', 'SES Access Key ID');
        Settings::set('ses_secret', $validated['ses_secret'] ?? '', 'string', 'email', 'SES Secret Access Key');
        Settings::set('ses_region', $validated['ses_region'] ?? 'us-east-1', 'string', 'email', 'SES Region');

        // Mailgun Settings
        Settings::set('mailgun_domain', $validated['mailgun_domain'] ?? '', 'string', 'email', 'Mailgun Domain');
        Settings::set('mailgun_secret', $validated['mailgun_secret'] ?? '', 'string', 'email', 'Mailgun Secret Key');
        Settings::set('mailgun_endpoint', $validated['mailgun_endpoint'] ?? 'api.mailgun.net', 'string', 'email', 'Mailgun Endpoint');

        Settings::clearCache();
        SettingsHelper::applyGlobalSettings();

        $activeTab = $request->input('active_tab', 'general');

        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        return redirect()->route('admin.settings.index', ['tab' => $activeTab])
            ->with('success', 'Configurações Globais Aplicadas e Cache Atualizado.');
    }

    /**
     * Send a test email.
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            // Re-apply settings just in case they were just saved
            \App\Helpers\SettingsHelper::applyGlobalSettings();

            $siteName = Settings::get('site_name', 'Vertex CBAV');
            $toEmail = $request->email;

            \Illuminate\Support\Facades\Mail::raw("Este é um e-mail de teste enviado do sistema {$siteName} para validar as configurações de SMTP/Serviço de e-mail.", function ($message) use ($toEmail, $siteName) {
                $message->to($toEmail)
                    ->subject("Teste de E-mail - {$siteName}");
            });

            return response()->json([
                'success' => true,
                'message' => 'E-mail de teste enviado com sucesso! Verifique sua caixa de entrada (e pasta de spam).',
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar e-mail de teste: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar e-mail de teste.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Activate maintenance mode (writes custom maintenance.php, sets bypass cookie for current admin).
     */
    public function activateMaintenance(Request $request, MaintenanceModeService $maintenance)
    {
        if ($maintenance->isActive()) {
            return redirect()->route('admin.settings.index', ['tab' => 'general'])
                ->with('info', 'O site já está em manutenção.');
        }

        try {
            $secret = $maintenance->activate();
            $user = Auth::user();
            $name = $user ? $user->name ?? $user->email : 'Sistema';
            Log::info('Manutenção ativada', [
                'action' => 'maintenance_activated',
                'user_id' => $user?->id,
                'user_name' => $name,
                'ip' => $request->ip(),
            ]);

            $bypassUrl = route('admin.acesso-mestre') . '?secret=' . $secret;

            return redirect()
                ->route('admin.settings.index', ['tab' => 'general'])
                ->with('success', 'Manutenção ativada. Use o link de bypass em outra aba/dispositivo se precisar.')
                ->with('maintenance_bypass_url', $bypassUrl)
                ->cookie('laravel_maintenance', $secret, 60 * 24 * 7, '/', null, $request->secure(), true, false, 'lax');
        } catch (\Throwable $e) {
            Log::error('Falha ao ativar manutenção: ' . $e->getMessage());
            return redirect()->route('admin.settings.index', ['tab' => 'general'])
                ->with('error', 'Não foi possível ativar a manutenção. Verifique permissões em storage/framework.');
        }
    }

    /**
     * Deactivate maintenance mode (removes maintenance.php).
     */
    public function deactivateMaintenance(Request $request, MaintenanceModeService $maintenance)
    {
        if (! $maintenance->isActive()) {
            return redirect()->route('admin.settings.index', ['tab' => 'general'])
                ->with('info', 'O site não está em manutenção.');
        }

        try {
            $maintenance->deactivate();
            $user = Auth::user();
            $name = $user ? $user->name ?? $user->email : 'Sistema';
            Log::info('Manutenção desativada', [
                'action' => 'maintenance_deactivated',
                'user_id' => $user?->id,
                'user_name' => $name,
                'ip' => $request->ip(),
            ]);

            return redirect()->route('admin.settings.index', ['tab' => 'general'])
                ->with('success', 'Manutenção desativada. O site está no ar.');
        } catch (\Throwable $e) {
            Log::error('Falha ao desativar manutenção: ' . $e->getMessage());
            return redirect()->route('admin.settings.index', ['tab' => 'general'])
                ->with('error', 'Não foi possível desativar a manutenção. Tente remover manualmente o arquivo storage/framework/maintenance.php.');
        }
    }
}
