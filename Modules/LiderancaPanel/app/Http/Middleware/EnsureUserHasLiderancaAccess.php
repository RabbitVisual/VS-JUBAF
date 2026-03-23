<?php

namespace Modules\LiderancaPanel\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasLiderancaAccess
{
    /**
     * Handle an incoming request.
     * Allow Admin users to access the Lideranca panel.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            abort(403, 'Acesso não autorizado.');
        }

        if (! auth()->user()->hasAdminAccess()) {
            abort(403, 'Acesso restrito ao painel de lideranca.');
        }

        return $next($request);
    }
}
