<?php

use Illuminate\Support\Facades\Route;
use Modules\Bible\App\Http\Controllers\BibleController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('bibles/compare', [BibleController::class, 'compare'])->name('bible.compare');
    Route::resource('bibles', BibleController::class)->names('bible.web');

    // Admin Routes (admin/bible plans, import, api) → centralizadas em routes/admin.php

    // Member Panel Routes
    Route::prefix('social/bible/plans')->name('member.bible.')->group(function () {
        Route::get('/', [\Modules\Bible\App\Http\Controllers\MemberPanel\ReadingPlanController::class, 'index'])->name('plans.index');
        Route::get('/catalog', [\Modules\Bible\App\Http\Controllers\MemberPanel\ReadingPlanController::class, 'catalog'])->name('plans.catalog');
        Route::get('/{id}/preview', [\Modules\Bible\App\Http\Controllers\MemberPanel\ReadingPlanController::class, 'preview'])->name('plans.preview');
        Route::post('/{id}/subscribe', [\Modules\Bible\App\Http\Controllers\MemberPanel\ReadingPlanController::class, 'subscribe'])->name('plans.subscribe');
        Route::get('/resume/{id}', [\Modules\Bible\App\Http\Controllers\MemberPanel\ReadingPlanController::class, 'show'])->name('plans.show');
        Route::post('/{subscriptionId}/recalculate', [\Modules\Bible\App\Http\Controllers\MemberPanel\ReadingPlanController::class, 'recalculate'])->name('plans.recalculate');
        Route::get('/download/{id}/pdf', [\Modules\Bible\App\Http\Controllers\MemberPanel\ReadingPlanController::class, 'downloadPdf'])->name('plans.pdf');

        // Reader
        Route::get('/read/{subscriptionId}/{day}', [\Modules\Bible\App\Http\Controllers\MemberPanel\PlanReaderController::class, 'read'])->name('reader');
        Route::post('/read/{subscriptionId}/{dayId}/complete', [\Modules\Bible\App\Http\Controllers\MemberPanel\PlanReaderController::class, 'complete'])->name('reader.complete');
        Route::post('/read/{subscriptionId}/{dayId}/uncomplete', [\Modules\Bible\App\Http\Controllers\MemberPanel\PlanReaderController::class, 'uncomplete'])->name('reader.uncomplete');
        Route::get('/read/{subscriptionId}/{dayId}/congratulations', [\Modules\Bible\App\Http\Controllers\MemberPanel\PlanReaderController::class, 'congratulations'])->name('reader.congratulations');
        Route::post('/read/{subscriptionId}/{dayId}/note', [\Modules\Bible\App\Http\Controllers\MemberPanel\PlanReaderController::class, 'storeNote'])->name('reader.note.store');

        // Search
        Route::get('/search', [\Modules\Bible\App\Http\Controllers\MemberPanel\BibleController::class, 'search'])->name('search');
        Route::get('/api/find', [\Modules\Bible\App\Http\Controllers\MemberPanel\BibleController::class, 'performSearch'])->name('api.search');
    });

    // Bible Favorites (Highlights)
    Route::prefix('social/bible/favorites')->name('member.bible.favorites.')->group(function () {
        Route::post('/batch', [\Modules\Bible\App\Http\Controllers\MemberPanel\FavoriteController::class, 'batchUpdate'])->name('batch');
        Route::post('/{id}', [\Modules\Bible\App\Http\Controllers\MemberPanel\FavoriteController::class, 'toggle'])->name('toggle');
        Route::delete('/{id}', [\Modules\Bible\App\Http\Controllers\MemberPanel\FavoriteController::class, 'destroy'])->name('destroy');
    });
});
