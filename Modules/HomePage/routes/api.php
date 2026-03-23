<?php

use Illuminate\Support\Facades\Route;
use Modules\HomePage\App\Http\Controllers\HomePageController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('homepages', HomePageController::class)->names('homepage');
});
