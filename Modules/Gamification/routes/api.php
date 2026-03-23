<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Gamification module API routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // API for bot/insights can be added here when needed
});
