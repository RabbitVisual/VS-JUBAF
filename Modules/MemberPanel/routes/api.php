<?php

use Illuminate\Support\Facades\Route;
use Modules\MemberPanel\App\Http\Controllers\MemberPanelController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('memberpanels', MemberPanelController::class)->names('memberpanel');
});
