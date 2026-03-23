<?php

use Illuminate\Support\Facades\Route;
use Modules\Igrejas\App\Http\Controllers\AdminIgrejaController;

Route::middleware(['auth', 'verified', 'can:gerenciar igrejas'])->group(function () {
    Route::prefix('admin/igrejas')->name('admin.igrejas.')->group(function () {
        Route::get('/', [AdminIgrejaController::class, 'index'])->name('index');
        Route::get('/create', [AdminIgrejaController::class, 'create'])->name('create');
        Route::post('/', [AdminIgrejaController::class, 'store'])->name('store');
        Route::get('/{igreja}/edit', [AdminIgrejaController::class, 'edit'])->name('edit');
        Route::put('/{igreja}', [AdminIgrejaController::class, 'update'])->name('update');
        Route::delete('/{igreja}', [AdminIgrejaController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('lideranca/igrejas')->name('lideranca.igrejas.')->group(function () {
        Route::get('/', [AdminIgrejaController::class, 'index'])->name('index');
        Route::get('/create', [AdminIgrejaController::class, 'create'])->name('create');
        Route::post('/', [AdminIgrejaController::class, 'store'])->name('store');
        Route::get('/{igreja}/edit', [AdminIgrejaController::class, 'edit'])->name('edit');
        Route::put('/{igreja}', [AdminIgrejaController::class, 'update'])->name('update');
        Route::delete('/{igreja}', [AdminIgrejaController::class, 'destroy'])->name('destroy');
    });
});
