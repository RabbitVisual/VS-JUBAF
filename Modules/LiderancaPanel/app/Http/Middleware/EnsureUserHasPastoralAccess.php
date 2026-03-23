<?php

namespace Modules\LiderancaPanel\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasliderancaalAccess
{
    /**
     * Handle an incoming request.
     * Allow Admin and lideranca to access the Gabinete liderancaal.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            abort(403, 'Acesso não autorizado.');
        }

        if (! auth()->user()->hasAdminAccess()) {
            abort(403, 'Acesso restrito ao Gabinete liderancaal.');
        }

        return $next($request);
    }
}
