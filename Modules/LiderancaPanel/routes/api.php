<?php

use Illuminate\Support\Facades\Route;
use Modules\liderancapanel\Http\Controllers\liderancapanelController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('liderancapanels', liderancapanelController::class)->names('liderancapanel');
});
