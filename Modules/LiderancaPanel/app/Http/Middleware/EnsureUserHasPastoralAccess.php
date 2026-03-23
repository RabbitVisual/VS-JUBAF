<?php

namespace Modules\liderancapanel\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPastoralAccess
{
    /**
     * Handle an incoming request.
     * Allow Admin and Pastor to access the Gabinete Pastoral.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            abort(403, 'Acesso não autorizado.');
        }

        if (! auth()->user()->hasAdminAccess()) {
            abort(403, 'Acesso restrito ao Gabinete Pastoral.');
        }

        return $next($request);
    }
}
