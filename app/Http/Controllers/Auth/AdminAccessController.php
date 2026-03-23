<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\MaintenanceModeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Login exclusivo para administradores durante manutenção (rota em exceção).
 * URL: /admin/acesso-mestre
 */
class AdminAccessController extends Controller
{
    public function __construct(
        protected MaintenanceModeService $maintenance
    ) {}

    public function showForm(Request $request)
    {
        return view('auth.admin-acesso', [
            'bypassSecret' => $request->query('secret'),
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Informe o e-mail.',
            'password.required' => 'Informe a senha.',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Credenciais inválidas.',
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'Conta inativa.',
            ]);
        }

        $isAdmin = $user->role && $user->role->slug === 'admin';
        if (! $isAdmin) {
            throw ValidationException::withMessages([
                'email' => 'Acesso restrito a administradores.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Em manutenção: definir cookie de bypass com o segredo atual para o redirect para /admin não retornar 503
        if ($this->maintenance->isActive() && defined('LARAVEL_MAINTENANCE_SECRET')) {
            $secret = LARAVEL_MAINTENANCE_SECRET;
            return redirect()
                ->intended(route('admin.dashboard'))
                ->cookie('laravel_maintenance', $secret, 60 * 24 * 7, '/', null, request()->secure(), true, false, 'lax');
        }

        return redirect()->intended(route('admin.dashboard'));
    }
}
