<?php

namespace Modules\Intercessor\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIntercessorStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!\Modules\Intercessor\App\Services\IntercessorSettings::get('module_enabled')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Módulo de Intercessão desativado temporariamente.'], 403);
            }
            return redirect()->route('member.dashboard')->with('error', 'O Módulo de Intercessão está desativado no momento.');
        }

        if (! $request->user()) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
