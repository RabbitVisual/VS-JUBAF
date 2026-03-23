<?php

use Illuminate\Support\Facades\Route;
use Modules\Diretoria\Http\Controllers\DiretoriaController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('diretorias', DiretoriaController::class)->names('diretoria');
});
