<?php

use Illuminate\Support\Facades\Route;
use Modules\Igrejas\Http\Controllers\IgrejasController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('igrejas', IgrejasController::class)->names('igrejas');
});
