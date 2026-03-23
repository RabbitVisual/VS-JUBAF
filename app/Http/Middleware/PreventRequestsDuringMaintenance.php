<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventRequestsDuringMaintenance
{
    /**
     * Path prefixes that remain accessible during maintenance.
     */
    protected array $except = [
        'biblia-online',
        'api/v1/bible', // busca e comparação de versões na Bíblia durante manutenção
        'login',
        'admin', // painel admin (auth/admin protege; acesso-mestre e dashboard acessíveis durante manutenção)
        'build',
        'storage',
        'vendor',
        'favicon.ico',
        'up', // health check
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->inMaintenance()) {
            return $next($request);
        }

        if ($this->hasValidBypassCookie($request)) {
            return $next($request);
        }

        if ($this->isExceptPath($request)) {
            return $next($request);
        }

        return response()->view('errors.503', [], 503);
    }

    protected function inMaintenance(): bool
    {
        return defined('LARAVEL_MAINTENANCE_SECRET');
    }

    protected function hasValidBypassCookie(Request $request): bool
    {
        if (! defined('LARAVEL_MAINTENANCE_SECRET')) {
            return false;
        }

        $cookie = $request->cookie('laravel_maintenance');

        return $cookie !== null && hash_equals(LARAVEL_MAINTENANCE_SECRET, $cookie);
    }

    protected function isExceptPath(Request $request): bool
    {
        $path = $request->path();

        foreach ($this->except as $except) {
            if ($except === '') {
                continue;
            }
            if ($path === $except || str_starts_with($path, $except.'/')) {
                return true;
            }
        }

        return false;
    }
}
