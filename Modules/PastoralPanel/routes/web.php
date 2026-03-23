<?php

use Illuminate\Support\Facades\Route;
use Modules\PastoralPanel\Http\Controllers\PastoralPanelController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('pastoralpanels', PastoralPanelController::class)->names('pastoralpanel');
});
