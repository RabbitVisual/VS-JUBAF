<?php

use Illuminate\Support\Facades\Route;
use Modules\liderancapanel\Http\Controllers\liderancapanelController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('liderancapanels', liderancapanelController::class)->names('liderancapanel');
});
