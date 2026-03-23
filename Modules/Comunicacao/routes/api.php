<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Endpoints API do módulo Comunicação podem ser registrados aqui.
});
