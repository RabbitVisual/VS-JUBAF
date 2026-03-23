<?php

use Illuminate\Support\Facades\Route;
use Modules\Comunicacao\Http\Controllers\ComunicacaoController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('comunicacaos', ComunicacaoController::class)->names('comunicacao');
});
