<?php

use Illuminate\Support\Facades\Route;
use Modules\PastoralPanel\Http\Controllers\PastoralPanelController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pastoralpanels', PastoralPanelController::class)->names('pastoralpanel');
});
