<?php

use Illuminate\Support\Facades\Route;
use Modules\Comunicacao\Http\Controllers\AdminPostagemController;
use Modules\Comunicacao\Http\Controllers\FeedController;

Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::get('/mural', [FeedController::class, 'index'])->name('mural.index');
});

Route::middleware(['web', 'auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('comunicacao/postagens', AdminPostagemController::class)
        ->except(['show'])
        ->parameters(['postagens' => 'postagem'])
        ->names([
            'index' => 'comunicacao.postagens.index',
            'create' => 'comunicacao.postagens.create',
            'store' => 'comunicacao.postagens.store',
            'edit' => 'comunicacao.postagens.edit',
            'update' => 'comunicacao.postagens.update',
            'destroy' => 'comunicacao.postagens.destroy',
        ]);
});
