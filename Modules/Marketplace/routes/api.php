<?php

use Illuminate\Support\Facades\Route;
use Modules\Marketplace\Http\Controllers\MarketplaceController;
use Modules\Marketplace\Http\Controllers\Public\FreightApiController;

Route::prefix('v1')->group(function () {
    Route::post('marketplace/freight', [FreightApiController::class, 'calculate'])
        ->name('api.v1.marketplace.freight');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::apiResource('marketplaces', MarketplaceController::class)->names('marketplace');
    });
});
