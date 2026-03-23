<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Projection Web Routes
|--------------------------------------------------------------------------
| API única: /api/v1/projection/* (definida em routes/api.php).
| Redirect legado: /projection -> admin.projection.index.
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('projection', function () {
        return redirect()->route('admin.projection.index');
    })->name('projection.index');
});
