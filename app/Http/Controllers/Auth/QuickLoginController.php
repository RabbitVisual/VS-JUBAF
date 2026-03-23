<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $type = $request->input('type'); // 'admin' or 'member'

        $email = match ($type) {
            'admin' => 'admin@demo.com',
            'member' => 'membro@demo.com',
            default => null,
        };

        if (! $email) {
            return back()->withErrors(['email' => 'Tipo de usuário inválido.']);
        }

        $user = User::with('role')->where('email', $email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Usuário demo não encontrado. Execute: php artisan db:seed --class=DemoUsersSeeder']);
        }

        Auth::login($user, $request->boolean('remember', true));

        $request->session()->regenerate();

        // Redirect based on role
        if ($user->role && $user->role->slug === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('memberpanel.dashboard'));
    }
}
