<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class QuickLoginController extends Controller
{
    /**
     * Quick login for demo users (only in development)
     */
    public function quickLogin(Request $request)
    {
        // Only allow in development mode
        if (! app()->environment('local', 'development', 'dev')) {
            abort(403, 'Quick login is only available in development mode.');
        }

        $type = (string) $request->input('type');

        $profile = $this->resolveProfile($type);
        if (! $profile) {
            return back()->withErrors(['email' => 'Tipo de usuário inválido.']);
        }

        $user = $this->findOrCreateDevUser($profile);

        Auth::login($user, $request->boolean('remember', true));

        $request->session()->regenerate();

        if ($profile['redirect'] === 'admin.dashboard') {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($profile['redirect'] === 'lideranca.dashboard' && Route::has('lideranca.dashboard')) {
            return redirect()->intended(route('lideranca.dashboard'));
        }

        return redirect()->intended(route('memberpanel.dashboard'));
    }

    /**
     * @return array{name:string,sobrenome:string,email:string,password:string,role:string,redirect:string}|null
     */
    private function resolveProfile(string $type): ?array
    {
        return match ($type) {
            'super_admin', 'superadmin', 'admin' => [
                'name' => 'Super',
                'sobrenome' => 'Admin Demo',
                'email' => 'superadmin@jubaf.com.br',
                'password' => 'password',
                'role' => 'Super Admin',
                'redirect' => 'admin.dashboard',
            ],
            'lideranca', 'leadership' => [
                'name' => 'Liderança',
                'sobrenome' => 'Demo',
                'email' => 'lideranca@jubaf.com.br',
                'password' => 'password',
                'role' => 'Líder Local',
                'redirect' => 'lideranca.dashboard',
            ],
            'membro', 'member' => [
                'name' => 'Membro',
                'sobrenome' => 'Demo',
                'email' => 'membro@jubaf.com.br',
                'password' => 'password',
                'role' => 'Jovem',
                'redirect' => 'memberpanel.dashboard',
            ],
            default => null,
        };
    }

    /**
     * @param  array{name:string,sobrenome:string,email:string,password:string,role:string,redirect:string}  $profile
     */
    private function findOrCreateDevUser(array $profile): User
    {
        $user = User::withoutEvents(function () use ($profile) {
            return User::updateOrCreate(
                ['email' => $profile['email']],
                [
                    'name' => $profile['name'],
                    'sobrenome' => $profile['sobrenome'],
                    'password' => bcrypt($profile['password']),
                    'is_active' => true,
                ]
            );
        });

        $user->syncRoles([$profile['role']]);

        return $user;
    }
}
