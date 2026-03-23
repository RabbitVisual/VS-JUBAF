<?php

use Illuminate\Support\Facades\Route;
use Modules\Diretoria\Http\Controllers\DiretoriaController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('diretorias', DiretoriaController::class)->names('diretoria');
});
