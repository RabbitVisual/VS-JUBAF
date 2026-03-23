<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('painel')->name('memberpanel.')->group(function () {
        Route::get('/', [\Modules\MemberPanel\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [\Modules\MemberPanel\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index');

        Route::get('/perfil', [\Modules\MemberPanel\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/perfil/editar', [\Modules\MemberPanel\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/perfil', [\Modules\MemberPanel\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::post('/perfil/fotos/{photo}/set-active', [\Modules\MemberPanel\App\Http\Controllers\ProfileController::class, 'setActivePhoto'])->name('profile.photo.active');
        Route::delete('/perfil/fotos/{photo}', [\Modules\MemberPanel\App\Http\Controllers\ProfileController::class, 'deletePhoto'])->name('profile.photo.destroy');

        Route::get('/vinculos', [\Modules\MemberPanel\App\Http\Controllers\RelationshipController::class, 'pending'])->name('relationships.pending');
        Route::get('/vinculos/criar', [\Modules\MemberPanel\App\Http\Controllers\RelationshipController::class, 'create'])->name('relationships.create');
        Route::post('/vinculos', [\Modules\MemberPanel\App\Http\Controllers\RelationshipController::class, 'store'])->name('relationships.store');
        Route::get('/vinculos/buscar-cpf', [\Modules\MemberPanel\App\Http\Controllers\RelationshipController::class, 'searchMemberByCpf'])->name('relationships.search-cpf');
        Route::post('/vinculos/{user_relationship}/aceitar', [\Modules\MemberPanel\App\Http\Controllers\RelationshipController::class, 'accept'])->name('relationships.accept');
        Route::post('/vinculos/{user_relationship}/recusar', [\Modules\MemberPanel\App\Http\Controllers\RelationshipController::class, 'reject'])->name('relationships.reject');

        Route::get('/notificacoes', [\Modules\MemberPanel\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notificacoes/{notification}/read', [\Modules\MemberPanel\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notificacoes/read-all', [\Modules\MemberPanel\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::delete('/notificacoes/clear-all', [\Modules\MemberPanel\App\Http\Controllers\NotificationController::class, 'clearAll'])->name('notifications.clear-all');
        Route::delete('/notificacoes/{notification}', [\Modules\MemberPanel\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');

        Route::get('/preferencias/notificacoes', [\Modules\Notifications\App\Http\Controllers\MemberPanel\NotificationPreferencesController::class, 'index'])->name('preferences.notifications.index');
        Route::put('/preferencias/notificacoes', [\Modules\Notifications\App\Http\Controllers\MemberPanel\NotificationPreferencesController::class, 'update'])->name('preferences.notifications.update');

        Route::get('/biblia/interlinear', [\Modules\Bible\App\Http\Controllers\InterlinearController::class, 'index'])->name('bible.interlinear');
        Route::get('/biblia/interlinear/data', [\Modules\Bible\App\Http\Controllers\InterlinearController::class, 'getData'])->name('bible.interlinear.data');
        Route::get('/biblia/interlinear/books', [\Modules\Bible\App\Http\Controllers\InterlinearController::class, 'getBooksMetadata'])->name('bible.interlinear.books');
        Route::get('/biblia/strong/{number}', [\Modules\Bible\App\Http\Controllers\InterlinearController::class, 'getStrongDefinition'])->name('bible.strong.show');
        Route::get('/biblia', [\Modules\Bible\App\Http\Controllers\MemberPanel\BibleController::class, 'index'])->name('bible.index');
        Route::get('/biblia/buscar', [\Modules\Bible\App\Http\Controllers\MemberPanel\BibleController::class, 'search'])->name('bible.search');
        Route::get('/biblia/favoritos', [\Modules\Bible\App\Http\Controllers\MemberPanel\BibleController::class, 'favorites'])->name('bible.favorites');
        Route::post('/biblia/versiculo/{verse}/favorito', [\Modules\Bible\App\Http\Controllers\MemberPanel\BibleController::class, 'addFavorite'])->name('bible.favorite.add');
        Route::delete('/biblia/versiculo/{verse}/favorito', [\Modules\Bible\App\Http\Controllers\MemberPanel\BibleController::class, 'removeFavorite'])->name('bible.favorite.remove');
        Route::get('/biblia/{version?}', [\Modules\Bible\App\Http\Controllers\MemberPanel\BibleController::class, 'read'])->name('bible.read');
        Route::get('/biblia/{version}/livro/{book}', [\Modules\Bible\App\Http\Controllers\MemberPanel\BibleController::class, 'showBook'])->name('bible.book');
        Route::get('/biblia/{version}/livro/{book}/capitulo/{chapter}', [\Modules\Bible\App\Http\Controllers\MemberPanel\BibleController::class, 'showChapter'])->name('bible.chapter');

        Route::prefix('tesouraria')->name('treasury.')->group(function () {
            $treasuryController = \Modules\Treasury\App\Http\Controllers\MemberPanel\TreasuryController::class;
            Route::get('/', [$treasuryController, 'dashboard'])->name('dashboard');
            Route::get('/dashboard', [$treasuryController, 'dashboard'])->name('dashboard.index');
            Route::get('/transparencia', [$treasuryController, 'transparency'])->name('transparency');
            Route::get('/entradas', [$treasuryController, 'entriesIndex'])->name('entries.index');
            Route::get('/campanhas', [$treasuryController, 'campaignsIndex'])->name('campaigns.index');
            Route::get('/metas', [$treasuryController, 'goalsIndex'])->name('goals.index');
            Route::get('/relatorios', [$treasuryController, 'reportsIndex'])->name('reports.index');
        });

        Route::get('/minhas-doacoes', [\Modules\PaymentGateway\App\Http\Controllers\MemberPanel\DonationController::class, 'index'])->name('donations.index');
        Route::get('/doacoes', [\Modules\PaymentGateway\App\Http\Controllers\MemberPanel\DonationController::class, 'create'])->name('donations.create');
        Route::post('/doacoes', [\Modules\PaymentGateway\App\Http\Controllers\MemberPanel\DonationController::class, 'store'])->name('donations.store');
        Route::get('/doacoes/{transactionId}', [\Modules\PaymentGateway\App\Http\Controllers\MemberPanel\DonationController::class, 'show'])->name('donations.show');
        Route::get('/doacoes/{transactionId}/retry', [\Modules\PaymentGateway\App\Http\Controllers\MemberPanel\DonationController::class, 'retry'])->name('donations.retry');
        Route::post('/doacoes/{transactionId}/retry', [\Modules\PaymentGateway\App\Http\Controllers\MemberPanel\DonationController::class, 'updateGateway'])->name('donations.update-gateway');
        Route::get('/doacoes/{transactionId}/status', [\Modules\PaymentGateway\App\Http\Controllers\MemberPanel\DonationController::class, 'checkStatus'])->name('donations.check-status');
    });

    Route::prefix('painel/eventos')->name('memberpanel.events.')->group(function () {
        Route::get('/minhas-inscricoes', [\Modules\Events\App\Http\Controllers\MemberPanel\EventController::class, 'myRegistrations'])->name('my-registrations');
        Route::get('/inscricoes', [\Modules\Events\App\Http\Controllers\MemberPanel\EventController::class, 'minhasInscricoes'])->name('minhas-inscricoes');
        Route::get('/minhas-inscricoes/{registration}', [\Modules\Events\App\Http\Controllers\MemberPanel\EventController::class, 'showRegistration'])->name('show-registration');
        Route::get('/', [\Modules\Events\App\Http\Controllers\MemberPanel\EventController::class, 'index'])->name('index');
        Route::get('/{event:slug}', [\Modules\Events\App\Http\Controllers\MemberPanel\EventController::class, 'show'])->name('show');
        Route::post('/{event:slug}/register', [\Modules\Events\App\Http\Controllers\MemberPanel\EventController::class, 'register'])->name('register');
        Route::post('/{event:slug}/inscrever', [\Modules\Events\App\Http\Controllers\MemberPanel\EventController::class, 'inscrever'])->name('inscrever');
    });

    Route::prefix('painel/sermoes')->name('memberpanel.sermons.')->group(function () {
        Route::get('/', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'index'])->name('index');
        Route::get('/meus-sermoes', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'mySermons'])->name('my-sermons');
        Route::get('/meus-favoritos', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'myFavorites'])->name('my-favorites');
        Route::get('/criar', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'create'])->name('create');
        Route::post('/', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'store'])->name('store');
        Route::get('/{sermon}/editar', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'edit'])->name('edit');
        Route::put('/{sermon}', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'update'])->name('update');
        Route::delete('/{sermon}', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'destroy'])->name('destroy');
        Route::get('/{sermon}', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'show'])->name('show');
        Route::post('/{sermon}/favoritar', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'toggleFavorite'])->name('toggle-favorite');
        Route::post('/{sermon}/comentar', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'storeComment'])->name('store-comment');
    });
});
