<?php

use Illuminate\Support\Facades\Route;
use Modules\Assets\App\Http\Controllers\MemberPanel\MyAssetsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Admin Routes (admin/assets) → centralizadas em routes/admin.php

// Member Panel Routes
Route::middleware(['auth', 'verified'])
    ->prefix('member-panel/assets')
    ->name('assets.memberpanel.')
    ->group(function () {
        Route::get('my-assets', [MyAssetsController::class, 'index'])->name('my-assets.index');
        Route::post('my-assets/{term}/sign', [MyAssetsController::class, 'signTerm'])->name('my-assets.sign');
    });
