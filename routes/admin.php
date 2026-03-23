<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Route::name('admin.')->group(function () {
        Route::get('/payment-gateways/statistics', [\Modules\PaymentGateway\App\Http\Controllers\Admin\PaymentGatewayController::class, 'statistics'])->name('payment-gateways.statistics');
        Route::resource('payment-gateways', \Modules\PaymentGateway\App\Http\Controllers\Admin\PaymentGatewayController::class)->except(['create', 'store', 'destroy', 'show']);
        Route::get('/transactions', [\Modules\PaymentGateway\App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/{payment}', [\Modules\PaymentGateway\App\Http\Controllers\Admin\TransactionController::class, 'show'])->name('transactions.show');
        Route::get('/transactions/{payment}/comprovante', [\Modules\PaymentGateway\App\Http\Controllers\Admin\TransactionController::class, 'receipt'])->name('transactions.receipt');
        Route::post('/transactions/{payment}/cancel', [\Modules\PaymentGateway\App\Http\Controllers\Admin\TransactionController::class, 'cancel'])->name('transactions.cancel');
        Route::delete('/transactions/{payment}', [\Modules\PaymentGateway\App\Http\Controllers\Admin\TransactionController::class, 'destroy'])->name('transactions.destroy');
    });

    Route::name('admin.')->group(function () {
        Route::get('/', [\Modules\Admin\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [\Modules\Admin\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index');

        Route::prefix('relatorios')->name('reports.')->group(function () {
            Route::get('demografico-familiar', [\Modules\Admin\App\Http\Controllers\FamilyDemographicsController::class, 'index'])->name('family-demographics.index');
            Route::get('demografico-familiar/exportar/pdf', [\Modules\Admin\App\Http\Controllers\FamilyDemographicsController::class, 'exportPdf'])->name('family-demographics.export.pdf');
            Route::get('demografico-familiar/exportar/excel', [\Modules\Admin\App\Http\Controllers\FamilyDemographicsController::class, 'exportExcel'])->name('family-demographics.export.excel');
        });

        Route::middleware([\Modules\Admin\App\Http\Middleware\EnsureUserIsTechnicalAdmin::class])->group(function () {
            Route::get('/modules', [\Modules\Admin\App\Http\Controllers\ModuleController::class, 'index'])->name('modules.index');
            Route::post('/modules/{module}/enable', [\Modules\Admin\App\Http\Controllers\ModuleController::class, 'enable'])->name('modules.enable');
            Route::post('/modules/{module}/disable', [\Modules\Admin\App\Http\Controllers\ModuleController::class, 'disable'])->name('modules.disable');
            Route::resource('cep-ranges', \Modules\Admin\App\Http\Controllers\CepRangeController::class);
            Route::get('/settings', [\Modules\Admin\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
            Route::put('/settings', [\Modules\Admin\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
            Route::post('/settings/test-email', [\Modules\Admin\App\Http\Controllers\SettingsController::class, 'testEmail'])->name('settings.test-email');
            Route::post('/settings/activate-maintenance', [\Modules\Admin\App\Http\Controllers\SettingsController::class, 'activateMaintenance'])->name('settings.activate-maintenance');
            Route::post('/settings/deactivate-maintenance', [\Modules\Admin\App\Http\Controllers\SettingsController::class, 'deactivateMaintenance'])->name('settings.deactivate-maintenance');
            Route::get('/password-resets', [\Modules\Admin\App\Http\Controllers\PasswordResetController::class, 'index'])->name('password-resets.index');
            Route::get('/password-resets/settings', [\Modules\Admin\App\Http\Controllers\PasswordResetController::class, 'settings'])->name('password-resets.settings');
            Route::put('/password-resets/settings', [\Modules\Admin\App\Http\Controllers\PasswordResetController::class, 'updateSettings'])->name('password-resets.settings.update');
        });

        Route::get('users/import', [\Modules\Admin\App\Http\Controllers\MemberImportController::class, 'showImportForm'])->name('users.import');
        Route::post('users/import', [\Modules\Admin\App\Http\Controllers\MemberImportController::class, 'import'])->name('users.import.post');
        Route::get('users/import/template', [\Modules\Admin\App\Http\Controllers\MemberImportController::class, 'downloadTemplate'])->name('users.import.template');
        Route::get('api/users/search', [\Modules\Admin\App\Http\Controllers\UserController::class, 'search'])->name('api.users.search');
        Route::get('api/users/search-by-cpf', [\Modules\Admin\App\Http\Controllers\UserController::class, 'searchByCpf'])->name('api.users.search-by-cpf');
        Route::get('users/{user}/family-tree-analysis', [\Modules\Admin\App\Http\Controllers\UserController::class, 'familyTreeAnalysis'])->name('users.family-tree-analysis');
        Route::resource('users', \Modules\Admin\App\Http\Controllers\UserController::class);

        Route::get('/profile', [\Modules\Admin\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [\Modules\Admin\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\Modules\Admin\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/profile/2fa', [\Modules\Admin\App\Http\Controllers\TwoFactorController::class, 'show'])->name('profile.2fa.show');
        Route::post('/profile/2fa/setup', [\Modules\Admin\App\Http\Controllers\TwoFactorController::class, 'setup'])->name('profile.2fa.setup');
        Route::post('/profile/2fa/confirm', [\Modules\Admin\App\Http\Controllers\TwoFactorController::class, 'confirm'])->name('profile.2fa.confirm');
        Route::post('/profile/2fa/disable', [\Modules\Admin\App\Http\Controllers\TwoFactorController::class, 'disable'])->name('profile.2fa.disable');
        Route::get('/profile/2fa/qr', [\Modules\Admin\App\Http\Controllers\TwoFactorController::class, 'qrImage'])->name('profile.2fa.qr');

        Route::delete('/notifications/clear-my-inbox', [\Modules\Admin\App\Http\Controllers\NotificationController::class, 'clearMyInbox'])->name('notifications.clear-my-inbox');
        Route::get('/notifications/control/dashboard', [\Modules\Notifications\App\Http\Controllers\Admin\NotificationDashboardController::class, 'index'])->name('notifications.control.dashboard');
        Route::get('/notifications/control/dlq', [\Modules\Notifications\App\Http\Controllers\Admin\NotificationDlqController::class, 'index'])->name('notifications.dlq.index');
        Route::post('/notifications/control/dlq/{failed}/retry', [\Modules\Notifications\App\Http\Controllers\Admin\NotificationDlqController::class, 'retry'])->name('notifications.dlq.retry');
        Route::get('/notifications/control/broadcast', [\Modules\Notifications\App\Http\Controllers\Admin\NotificationBroadcastController::class, 'create'])->name('notifications.broadcast.create');
        Route::post('/notifications/control/broadcast', [\Modules\Notifications\App\Http\Controllers\Admin\NotificationBroadcastController::class, 'store'])->name('notifications.broadcast.store');
        Route::resource('notifications/templates', \Modules\Notifications\App\Http\Controllers\Admin\NotificationTemplatesController::class)->parameters(['templates' => 'template'])->names('notifications.templates');
        Route::resource('notifications', \Modules\Admin\App\Http\Controllers\NotificationController::class);

        Route::prefix('homepage')->name('homepage.')->group(function () {
            Route::get('/', fn () => redirect()->route('admin.homepage.settings.index'))->name('index');
            Route::get('/settings', [\Modules\Admin\App\Http\Controllers\HomePageSettingsController::class, 'index'])->name('settings.index');
            Route::put('/settings', [\Modules\Admin\App\Http\Controllers\HomePageSettingsController::class, 'update'])->name('settings.update');
            Route::resource('carousel', \Modules\Admin\App\Http\Controllers\CarouselController::class)->except(['show'])->parameters(['carousel' => 'slide']);
            Route::post('/carousel/order', [\Modules\Admin\App\Http\Controllers\CarouselController::class, 'updateOrder'])->name('carousel.order');
            Route::post('/carousel/{slide}/toggle', [\Modules\Admin\App\Http\Controllers\CarouselController::class, 'toggleActive'])->name('carousel.toggle');
            Route::post('/carousel/{slide}/duplicate', [\Modules\Admin\App\Http\Controllers\CarouselController::class, 'duplicate'])->name('carousel.duplicate');
            Route::post('contacts/mark-read', [\Modules\Admin\App\Http\Controllers\ContactController::class, 'markRead'])->name('contacts.mark-read');
            Route::resource('contacts', \Modules\Admin\App\Http\Controllers\ContactController::class);
            Route::get('newsletter/export', [\Modules\Admin\App\Http\Controllers\NewsletterController::class, 'export'])->name('newsletter.export');
            Route::resource('newsletter', \Modules\Admin\App\Http\Controllers\NewsletterController::class);
            Route::post('/newsletter/send', [\Modules\Admin\App\Http\Controllers\NewsletterController::class, 'send'])->name('newsletter.send');
        });

        Route::prefix('bible')->name('bible.')->group(function () {
            Route::get('import', [\Modules\Bible\App\Http\Controllers\BibleController::class, 'import'])->name('import');
            Route::post('import', [\Modules\Bible\App\Http\Controllers\BibleController::class, 'storeImport'])->name('import.store');
            Route::resource('plans', \Modules\Bible\App\Http\Controllers\Admin\BiblePlanController::class);
            Route::get('plans/{id}/generate', [\Modules\Bible\App\Http\Controllers\Admin\BiblePlanController::class, 'generator'])->name('plans.generate');
            Route::post('plans/{id}/generate', [\Modules\Bible\App\Http\Controllers\Admin\BiblePlanController::class, 'processGeneration'])->name('plans.process-generation');
            Route::get('plans/{planId}/days/{dayId}/edit', [\Modules\Bible\App\Http\Controllers\Admin\BiblePlanController::class, 'editDay'])->name('plans.days.edit');
            Route::post('plans/days/{dayId}/content', [\Modules\Bible\App\Http\Controllers\Admin\BiblePlanController::class, 'storeContent'])->name('plans.content.store');
            Route::put('plans/content/{contentId}', [\Modules\Bible\App\Http\Controllers\Admin\BiblePlanController::class, 'updateContent'])->name('plans.content.update');
            Route::delete('plans/content/{contentId}', [\Modules\Bible\App\Http\Controllers\Admin\BiblePlanController::class, 'destroyContent'])->name('plans.content.destroy');
            Route::get('reports/church-plan', [\Modules\Bible\App\Http\Controllers\Admin\BibleReportController::class, 'churchPlan'])->name('reports.church-plan');
        });
        Route::resource('bible', \Modules\Bible\App\Http\Controllers\Admin\BibleController::class);
        Route::get('/bible/import', [\Modules\Bible\App\Http\Controllers\Admin\BibleController::class, 'importForm'])->name('bible.import');
        Route::post('/bible/import', [\Modules\Bible\App\Http\Controllers\Admin\BibleController::class, 'store'])->name('bible.import.store');
        Route::get('/bible/{version}/book/{book}', [\Modules\Bible\App\Http\Controllers\Admin\BibleController::class, 'viewBook'])->name('bible.book');
        Route::get('/bible/{version}/book/{book}/chapter/{chapter}', [\Modules\Bible\App\Http\Controllers\Admin\BibleController::class, 'viewChapter'])->name('bible.chapter');
        Route::get('/bible/{bible}/chapter-audio', [\Modules\Bible\App\Http\Controllers\Admin\BibleController::class, 'chapterAudioIndex'])->name('bible.chapter-audio.index');
        Route::get('/bible/{bible}/chapter-audio/template', [\Modules\Bible\App\Http\Controllers\Admin\BibleController::class, 'chapterAudioTemplate'])->name('bible.chapter-audio.template');
        Route::post('/bible/{bible}/chapter-audio', [\Modules\Bible\App\Http\Controllers\Admin\BibleController::class, 'chapterAudioStore'])->name('bible.chapter-audio.store');
        Route::delete('/bible/{bible}/chapter-audio/{chapter_audio}', [\Modules\Bible\App\Http\Controllers\Admin\BibleController::class, 'chapterAudioDestroy'])->name('bible.chapter-audio.destroy');
    });

    Route::prefix('sermons')->name('admin.sermons.')->group(function () {
        Route::resource('sermons', \Modules\Sermons\App\Http\Controllers\Admin\SermonController::class)->except(['show']);
        Route::get('/sermons/{sermon}', [\Modules\Sermons\App\Http\Controllers\Admin\SermonController::class, 'show'])->name('sermons.show');
        Route::get('/sermons/{sermon}/export-pdf', [\Modules\Sermons\App\Http\Controllers\Admin\SermonController::class, 'exportPdf'])->name('sermons.export-pdf');
        Route::post('/sermons/{sermon}/collaborators', [\Modules\Sermons\App\Http\Controllers\Admin\SermonController::class, 'inviteCollaborator'])->name('sermons.collaborators.invite');
        Route::resource('categories', \Modules\Sermons\App\Http\Controllers\Admin\CategoryController::class)->except(['show']);
        Route::resource('series', \Modules\Sermons\App\Http\Controllers\Admin\BibleSeriesController::class);
        Route::resource('studies', \Modules\Sermons\App\Http\Controllers\Admin\BibleStudyController::class);
        Route::resource('commentaries', \Modules\Sermons\App\Http\Controllers\Admin\BibleCommentaryController::class);
    });

    Route::prefix('events')->name('admin.events.')->group(function () {
        Route::resource('events', \Modules\Events\App\Http\Controllers\Admin\EventController::class);
        Route::post('events/{event}/duplicate', [\Modules\Events\App\Http\Controllers\Admin\EventController::class, 'duplicate'])->name('events.duplicate');
        Route::post('events/{event}/batches', [\Modules\Events\App\Http\Controllers\Admin\EventController::class, 'storeBatch'])->name('events.batches.store');
        Route::put('events/{event}/batches/{batch}', [\Modules\Events\App\Http\Controllers\Admin\EventController::class, 'updateBatch'])->name('events.batches.update');
        Route::delete('events/{event}/batches/{batch}', [\Modules\Events\App\Http\Controllers\Admin\EventController::class, 'destroyBatch'])->name('events.batches.destroy');
        Route::get('checkin', [\Modules\Events\App\Http\Controllers\Admin\CheckinController::class, 'index'])->name('checkin.index');
        Route::post('checkin/validate', [\Modules\Events\App\Http\Controllers\Admin\CheckinController::class, 'validateCheckin'])->name('checkin.validate');
    });

    Route::prefix('conselho')->name('admin.Diretoria.')->group(function () {
        $admindiretoria = \Modules\Diretoria\App\Http\Controllers\Admin\diretoriaController::class;
        Route::get('/', [$admindiretoria, 'index'])->name('index');
        Route::get('planejamento/homologacao', [$admindiretoria, 'planningApprovals'])->name('planning.index');
    });
});
