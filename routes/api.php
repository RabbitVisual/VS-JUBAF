<?php

use App\Http\Controllers\Api\CepController;
use Illuminate\Support\Facades\Route;

Route::prefix('cep')->name('cep.')->group(function () {
    Route::get('/buscar', [CepController::class, 'buscar'])->name('buscar');
    Route::get('/validar', [CepController::class, 'validar'])->name('validar');
    Route::get('/cidades/{uf}', [CepController::class, 'cidadesPorUf'])->name('cidades.uf');
    Route::get('/cidades', [CepController::class, 'cidadesPorNome'])->name('cidades.nome');
});

Route::prefix('cep-ranges')->name('cep-ranges.')->group(function () {
    Route::get('/locations', [\Modules\Admin\App\Http\Controllers\CepRangeController::class, 'getLocations'])->name('locations');
});

$notificationsV1 = \Modules\Notifications\App\Http\Controllers\Api\V1\NotificationController::class;
Route::middleware(['web', 'auth'])->prefix('v1/notifications')->name('notifications.api.')->group(function () use ($notificationsV1) {
    Route::get('/', [$notificationsV1, 'index'])->name('index');
    Route::get('/unread-count', [$notificationsV1, 'unreadCount'])->name('unread-count');
    Route::post('/read-all', [$notificationsV1, 'markAllAsRead'])->name('read-all');
    Route::delete('/clear-all', [$notificationsV1, 'clearAll'])->name('clear-all');
    Route::post('/{userNotification}/read', [$notificationsV1, 'markAsRead'])->name('read');
    Route::delete('/{userNotification}', [$notificationsV1, 'destroy'])->name('destroy');
});

$bibleV1 = \Modules\Bible\App\Http\Controllers\Api\V1\BibleController::class;
Route::middleware(['throttle:60,1'])->prefix('v1/bible')->name('bible.api.')->group(function () use ($bibleV1) {
    Route::get('/versions', [$bibleV1, 'versions'])->name('versions');
    Route::get('/books', [$bibleV1, 'books'])->name('books');
    Route::get('/chapters', [$bibleV1, 'chapters'])->name('chapters');
    Route::get('/verses', [$bibleV1, 'verses'])->name('verses');
    Route::get('/find', [$bibleV1, 'find'])->name('find');
    Route::get('/search', [$bibleV1, 'search'])->name('search');
    Route::get('/random', [$bibleV1, 'random'])->name('random');
    Route::get('/compare', [$bibleV1, 'compare'])->name('compare');
    Route::get('/audio-url', [$bibleV1, 'audioUrl'])->name('audio-url');
    Route::get('/panorama', [$bibleV1, 'panorama'])->name('panorama');
});

$paymentGatewayV1 = \Modules\PaymentGateway\App\Http\Controllers\Api\V1\PaymentGatewayController::class;
Route::middleware(['throttle:60,1'])->prefix('v1')->group(function () use ($paymentGatewayV1) {
    Route::get('payment-gateways', [$paymentGatewayV1, 'index'])->name('api.payment-gateways.index');
    Route::get('payments/status', [$paymentGatewayV1, 'paymentStatus'])->name('api.payments.status');
    Route::get('payments/{transactionId}/status', [$paymentGatewayV1, 'paymentStatus'])->name('api.payments.status.show');
});

Route::middleware(['throttle:120,1'])->prefix('v1/gateway')->name('api.')->group(function () {
    Route::post('/webhook/{driver}', [\Modules\PaymentGateway\App\Http\Controllers\GatewayWebhookController::class, 'handle'])->name('gateway.webhook');
});

if (config('app.debug')) {
    Route::get('/debug/simulate-payment/{driver}', [\Modules\PaymentGateway\App\Http\Controllers\DebugController::class, 'simulatePayment'])->name('debug.simulate-payment');
}

$authV1 = \App\Http\Controllers\Api\V1\AuthController::class;
Route::middleware(['throttle:10,1'])->prefix('v1/auth')->name('auth.api.')->group(function () use ($authV1) {
    Route::post('desktop-login', [$authV1, 'desktopLogin'])->name('desktop-login');
});

$treasuryV1 = \Modules\Treasury\App\Http\Controllers\Api\V1\TreasuryController::class;
Route::middleware(['throttle:60,1', 'web', 'auth'])->prefix('v1/treasury')->name('treasury.api.')->group(function () use ($treasuryV1) {
    Route::get('dashboard', [$treasuryV1, 'dashboard'])->name('dashboard');
    Route::get('entry-form-options', [$treasuryV1, 'entryFormOptions'])->name('entry-form-options');
    Route::get('entries', [$treasuryV1, 'entries'])->name('entries.index');
    Route::post('entries', [$treasuryV1, 'storeEntry'])->name('entries.store');
    Route::get('entries/{id}', [$treasuryV1, 'entry'])->name('entries.show');
    Route::put('entries/{id}', [$treasuryV1, 'updateEntry'])->name('entries.update');
    Route::delete('entries/{id}', [$treasuryV1, 'destroyEntry'])->name('entries.destroy');
    Route::get('campaigns', [$treasuryV1, 'campaigns'])->name('campaigns.index');
    Route::get('goals', [$treasuryV1, 'goals'])->name('goals.index');
    Route::get('reports', [$treasuryV1, 'reports'])->name('reports.index');
    Route::get('permissions', [$treasuryV1, 'permissions'])->name('permissions.index');
});

$DiretoriaV1 = \Modules\Diretoria\App\Http\Controllers\Api\V1\DiretoriaController::class;
Route::middleware(['throttle:60,1', 'web', 'auth'])->prefix('v1/church-diretoria')->name('Diretoria.api.')->group(function () use ($DiretoriaV1) {
    Route::get('/', [$DiretoriaV1, 'index'])->name('index');
    Route::get('/members', [$DiretoriaV1, 'members'])->name('members');
    Route::get('/agendas', [$DiretoriaV1, 'agendas'])->name('agendas');
    Route::get('/approvals', [$DiretoriaV1, 'approvals'])->name('approvals');
    Route::get('/documents', [$DiretoriaV1, 'documents'])->name('documents');
});

$sermonsV1 = \Modules\Sermons\App\Http\Controllers\Api\V1\SermonController::class;
Route::middleware(['throttle:60,1'])->prefix('v1/sermons')->name('sermons.api.')->group(function () use ($sermonsV1) {
    Route::get('/', [$sermonsV1, 'index'])->name('index');
    Route::get('/slug/{slug}', [$sermonsV1, 'showBySlug'])->name('show-by-slug');
    Route::get('/{id}', [$sermonsV1, 'show'])->name('show');
});
