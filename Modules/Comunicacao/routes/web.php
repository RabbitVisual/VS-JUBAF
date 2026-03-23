<?php

use Illuminate\Support\Facades\Route;
use Modules\Comunicacao\Http\Controllers\ComunicacaoController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('comunicacaos', ComunicacaoController::class)->names('comunicacao');
});
