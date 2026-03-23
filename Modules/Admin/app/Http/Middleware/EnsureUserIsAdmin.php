<?php

namespace Modules\Admin\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (! auth()->check()) {
            abort(403, 'Acesso não autorizado.');
        }

        $user = auth()->user();

        // Apenas Admin ou Pastor podem acessar o painel admin (incluindo conselho)
        if (! $user->hasAdminAccess()) {
            abort(403, 'Acesso restrito.');
        }

        return $next($request);
    }
}
