<?php

use Illuminate\Support\Facades\Route;
use Modules\Igrejas\Http\Controllers\IgrejasController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('igrejas', IgrejasController::class)->names('igrejas');
});
