<?php

use Illuminate\Support\Facades\Route;
use Modules\SocialAction\App\Http\Controllers\MemberPanel\MyImpactController;
use Modules\SocialAction\App\Http\Controllers\MemberPanel\MemberPrayerController;

Route::middleware(['auth', 'verified'])->group(function () {

    // Member Routes
    Route::prefix('social-action')->name('socialaction.member.')->group(function () {
        Route::get('/', [MyImpactController::class, 'index'])->name('impact.index');
        Route::get('/campaigns', [MyImpactController::class, 'campaigns'])->name('campaigns.index');

        // Pedidos de Oração
        Route::get('/prayer', [MemberPrayerController::class, 'index'])->name('prayer.index');
        Route::post('/prayer', [MemberPrayerController::class, 'store'])->name('prayer.store');
    });
});
