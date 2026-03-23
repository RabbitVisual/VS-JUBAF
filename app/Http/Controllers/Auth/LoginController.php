<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('homepage::auth.login', [
            'maintenanceMode' => false, // Manutenção real é controlada por Status do Site (artisan down/up)
        ]);
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Validate reCAPTCHA if enabled (desabilitado em ambiente local/dev)
        $recaptchaEnabled = ! app()->environment('local', 'development', 'dev')
            && Settings::get('recaptcha_enabled', false);
        if ($recaptchaEnabled) {
            $request->validate([
                'g-recaptcha-response' => 'required|string',
            ], [
                'g-recaptcha-response.required' => 'Por favor, complete o reCAPTCHA para continuar.',
            ]);

            $secretKey = Settings::get('recaptcha_secret_key');

            if (! $secretKey) {
                \Log::error('reCAPTCHA habilitado mas chave secreta não configurada.');

                return back()->withErrors(['login' => 'Erro interno na validação de segurança. Entre em contato com o suporte.'])->withInput();
            }

            // Verify with Google
            $verifyResponse = \Illuminate\Support\Facades\Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ]);

            if (! $verifyResponse->successful() || ! $verifyResponse->json('success')) {
                throw ValidationException::withMessages([
                    'g-recaptcha-response' => 'Falha na verificação do reCAPTCHA. Por favor, tente novamente.',
                ]);
            }

            // reCAPTCHA v3: check score threshold
            $recaptchaVersion = config('services.recaptcha.version', 'v2');
            if ($recaptchaVersion === 'v3') {
                $score = (float) $verifyResponse->json('score', 0);
                $threshold = (float) config('services.recaptcha.v3_score_threshold', 0.5);
                if ($score < $threshold) {
                    throw ValidationException::withMessages([
                        'g-recaptcha-response' => 'Verificação de segurança não passou. Tente novamente.',
                    ]);
                }
            }
        }

        // Validate the request
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ], [
            'identifier.required' => 'Por favor, informe seu e-mail ou CPF.',
            'password.required' => 'Por favor, informe sua senha.',
        ]);

        $identifier = $request->input('identifier');
        $password = $request->input('password');
        $remember = $request->boolean('remember', false);
        $loginType = $request->input('login_type', 'email');

        // Determine if identifier is CPF or email
        $cleanedIdentifier = preg_replace('/[^0-9]/', '', $identifier);

        // If login_type is cpf or if the cleaned identifier has 11 digits (CPF)
        if ($loginType === 'cpf' || strlen($cleanedIdentifier) === 11) {
            // Login by CPF - Robust search stripping dots and dashes from DB field
            $user = User::whereRaw("REPLACE(REPLACE(cpf, '.', ''), '-', '') = ?", [$cleanedIdentifier])->first();

            if (! $user) {
                throw ValidationException::withMessages([
                    'identifier' => 'CPF não encontrado em nossos registros.',
                ]);
            }
        } else {
            // Login by Email
            $user = User::where('email', $identifier)->first();

            if (! $user) {
                throw ValidationException::withMessages([
                    'identifier' => 'E-mail não encontrado em nossos registros.',
                ]);
            }
        }

        // Check if user is active
        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'identifier' => 'Sua conta está inativa. Entre em contato com a administração.',
            ]);
        }

        // Verify password
        if (! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Senha incorreta.',
            ]);
        }

        // 2FA: se estiver ativo globalmente e o usuário for admin com 2FA configurado, exige código TOTP
        $twoFactorEnabled = (bool) config('auth.2fa.enabled', false);
        $isAdmin = $user->role && $user->role->slug === 'admin';
        if ($twoFactorEnabled && $isAdmin && $user->hasTwoFactorEnabled()) {
            $request->session()->put('login.id', $user->id);
            $request->session()->put('login.remember', $remember);
            $request->session()->regenerate();

            return redirect()->route('login.2fa.form');
        }

        // Login the user
        Auth::login($user, $remember);
        $request->session()->regenerate();

        // Redirect based on role
        if ($user->role && $user->role->slug === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('memberpanel.dashboard'));
    }

    /**
     * Exibe o formulário para inserir o código 2FA (após senha válida de admin com 2FA ativo).
     */
    public function showTwoFactorForm()
    {
        if (! session()->has('login.id')) {
            return redirect()->route('login')->withErrors(['login' => 'Sessão expirada. Faça login novamente.']);
        }

        return view('homepage::auth.login-2fa');
    }

    /**
     * Valida o código 2FA e finaliza o login.
     */
    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6|regex:/^[0-9]+$/',
        ], [
            'code.required' => 'Informe o código de 6 dígitos.',
            'code.size' => 'O código deve ter exatamente 6 dígitos.',
        ]);

        $userId = session('login.id');
        $remember = (bool) session('login.remember', false);

        if (! $userId) {
            return redirect()->route('login')->withErrors(['login' => 'Sessão expirada. Faça login novamente.']);
        }

        $user = User::find($userId);
        if (! $user || ! $user->hasTwoFactorEnabled()) {
            session()->forget(['login.id', 'login.remember']);

            return redirect()->route('login')->withErrors(['login' => 'Usuário ou 2FA inválido. Tente novamente.']);
        }

        $verified = app(\App\Services\TwoFactorAuthService::class)->verify($user->two_factor_secret, $request->input('code'));
        if (! $verified) {
            throw ValidationException::withMessages([
                'code' => 'Código inválido. Verifique o aplicativo e tente novamente.',
            ]);
        }

        session()->forget(['login.id', 'login.remember']);
        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('homepage.index');
    }
}
