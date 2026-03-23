<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware('web')->group(base_path('routes/member.php'));
            Route::middleware('web')->group(base_path('routes/admin.php'));
            Route::middleware('web')->group(base_path('routes/lideranca.php'));
            // Treasury: garante rotas /treasury/* carregadas (evita 404 no sidebar admin)
            if (file_exists($treasuryRoutes = base_path('Modules/Treasury/routes/web.php'))) {
                Route::middleware('web')->group($treasuryRoutes);
            }
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(\App\Http\Middleware\PreventRequestsDuringMaintenance::class);
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'admin' => \Modules\Admin\App\Http\Middleware\EnsureUserIsAdmin::class,
            'lideranca' => \Modules\LiderancaPanel\App\Http\Middleware\EnsureUserHasLiderancaAccess::class,
            'optional_sanctum' => \App\Http\Middleware\OptionalSanctum::class,
        ]);
        // Webhook canônico de pagamento é POST /api/v1/gateway/webhook/{driver} (rota API, sem CSRF)
        $middleware->validateCsrfTokens(except: []);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
