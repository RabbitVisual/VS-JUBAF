<?php

namespace Modules\Admin\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsTechnicalAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and is Admin (not just Pastor)
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403, 'Acesso restrito a administradores técnicos.');
        }

        return $next($request);
    }
}
