<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'lideranca'])->prefix('lideranca')->name('lideranca.')->group(function () {
    Route::get('/', [\Modules\LiderancaPanel\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [\Modules\LiderancaPanel\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index');

    Route::prefix('rebanho')->name('rebanho.')->group(function () {
        Route::get('/', [\Modules\LiderancaPanel\App\Http\Controllers\RebanhoController::class, 'index'])->name('index');
        Route::get('/criar', [\Modules\Admin\App\Http\Controllers\UserController::class, 'create'])->name('create');
        Route::post('/', [\Modules\Admin\App\Http\Controllers\UserController::class, 'store'])->name('store');
        Route::get('/{user}', [\Modules\LiderancaPanel\App\Http\Controllers\RebanhoController::class, 'show'])->name('show');
        Route::get('/{user}/editar', [\Modules\Admin\App\Http\Controllers\UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [\Modules\Admin\App\Http\Controllers\UserController::class, 'update'])->name('update');
    });

    Route::prefix('sermoes')->name('sermoes.')->group(function () {
        Route::get('/', fn () => redirect()->route('lideranca.sermoes.sermons.index'))->name('index');
        Route::get('sermons/{sermon}/export-pdf', [\Modules\Sermons\App\Http\Controllers\Lideranca\SermonController::class, 'exportPdf'])->name('sermons.export-pdf');
        Route::post('sermons/{sermon}/collaborators', [\Modules\Sermons\App\Http\Controllers\Lideranca\SermonController::class, 'inviteCollaborator'])->name('sermons.collaborators.invite');
        Route::resource('sermons', \Modules\Sermons\App\Http\Controllers\Lideranca\SermonController::class);
        Route::resource('categories', \Modules\Sermons\App\Http\Controllers\Lideranca\CategoryController::class)->except(['show']);
        Route::resource('series', \Modules\Sermons\App\Http\Controllers\Lideranca\BibleSeriesController::class)->except(['show']);
        Route::resource('studies', \Modules\Sermons\App\Http\Controllers\Lideranca\BibleStudyController::class)->except(['show']);
        Route::resource('commentaries', \Modules\Sermons\App\Http\Controllers\Lideranca\BibleCommentaryController::class)->except(['show']);
    });

    Route::prefix('transparencia')->name('transparencia.')->group(function () {
        Route::get('/', [\Modules\LiderancaPanel\App\Http\Controllers\TransparenciaController::class, 'index'])->name('index');
    });

    Route::prefix('tesouraria')->name('tesouraria.')->group(function () {
        $dash = \Modules\Treasury\App\Http\Controllers\Lideranca\DashboardController::class;
        $entryCtrl = \Modules\Treasury\App\Http\Controllers\Lideranca\FinancialEntryController::class;
        $campaignCtrl = \Modules\Treasury\App\Http\Controllers\Lideranca\CampaignController::class;
        $goalCtrl = \Modules\Treasury\App\Http\Controllers\Lideranca\FinancialGoalController::class;
        $reportCtrl = \Modules\Treasury\App\Http\Controllers\Lideranca\ReportController::class;

        Route::get('/', [$dash, 'index'])->name('dashboard');
        Route::get('/dashboard', [$dash, 'index'])->name('dashboard.index');
        Route::get('/entries', [$entryCtrl, 'index'])->name('entries.index');
        Route::get('/entries/create', [$entryCtrl, 'create'])->name('entries.create');
        Route::post('/entries', [$entryCtrl, 'store'])->name('entries.store');
        Route::get('/entries/{entry}/edit', [$entryCtrl, 'edit'])->name('entries.edit');
        Route::put('/entries/{entry}', [$entryCtrl, 'update'])->name('entries.update');
        Route::delete('/entries/{entry}', [$entryCtrl, 'destroy'])->name('entries.destroy');
        Route::get('/campaigns', [$campaignCtrl, 'index'])->name('campaigns.index');
        Route::get('/goals', [$goalCtrl, 'index'])->name('goals.index');
        Route::get('/reports', [$reportCtrl, 'index'])->name('reports.index');
    });

    $conselhoCtrl = \Modules\LiderancaPanel\App\Http\Controllers\ConselhoController::class;
    Route::prefix('conselho')->name('conselho.')->group(function () use ($conselhoCtrl) {
        Route::get('/', [$conselhoCtrl, 'index'])->name('index');
        Route::get('/aprovacoes', [$conselhoCtrl, 'approvals'])->name('approvals');
        Route::get('/aprovacoes/{approval}', [$conselhoCtrl, 'showApproval'])->name('approvals.show');
        Route::post('/aprovacoes/{approval}/aprovar', [$conselhoCtrl, 'approve'])->name('approvals.approve');
        Route::post('/aprovacoes/{approval}/rejeitar', [$conselhoCtrl, 'reject'])->name('approvals.reject');
    });

    $eventosCtrl = \Modules\LiderancaPanel\App\Http\Controllers\EventosController::class;
    Route::prefix('eventos')->name('eventos.')->group(function () use ($eventosCtrl) {
        Route::get('/', [$eventosCtrl, 'index'])->name('index');
        Route::get('/check-in', [$eventosCtrl, 'checkinIndex'])->name('checkin.index');
        Route::post('/check-in/validar', [$eventosCtrl, 'checkinValidate'])->name('checkin.validate');
        Route::get('/{event}', [$eventosCtrl, 'show'])->name('show');
    });
});

