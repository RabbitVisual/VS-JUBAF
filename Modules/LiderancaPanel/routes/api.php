<?php

use Illuminate\Support\Facades\Route;
use Modules\LiderancaPanel\App\Http\Controllers\LiderancaPanelController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('liderancapanels', LiderancaPanelController::class)->names('liderancapanel');
});
