<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Intercessor Routes
|--------------------------------------------------------------------------
|
| Rotas públicas (sem login) para recebimento de pedidos de oração.
| As rotas de admin e member permanecem centralizadas em routes/admin.php
| e routes/member.php, conforme padrão do projeto.
|
*/

Route::middleware('web')->group(function () {
    Route::get('/pedidos-de-oracao', [\Modules\Intercessor\App\Http\Controllers\PublicSite\PublicPrayerRequestController::class, 'create'])
        ->name('public.intercessor.requests.create');

    Route::post('/pedidos-de-oracao', [\Modules\Intercessor\App\Http\Controllers\PublicSite\PublicPrayerRequestController::class, 'store'])
        ->name('public.intercessor.requests.store');
});
