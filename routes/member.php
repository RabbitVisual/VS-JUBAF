<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas do Painel do Membro (Centralizadas)
|--------------------------------------------------------------------------
|
| Todas as rotas destinadas a usuários autenticados (membros) no painel
| /painel. Middleware: auth, verified. Organizado por módulo para
| manutenção e segurança global.
|
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // =====================================================================
    // MemberPanel (Core): Dashboard, Perfil, Ministérios, Notificações, Bíblia, Tesouraria
    // =====================================================================
    Route::prefix('painel')->name('memberpanel.')->group(function () {
        // Dashboard
        Route::get('/', [\Modules\MemberPanel\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [\Modules\MemberPanel\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index');

        // Perfil
        Route::get('/perfil', [\Modules\MemberPanel\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/perfil/editar', [\Modules\MemberPanel\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/perfil', [\Modules\MemberPanel\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::post('/perfil/fotos/{photo}/set-active', [\Modules\MemberPanel\App\Http\Controllers\ProfileController::class, 'setActivePhoto'])->name('profile.photo.active');
        Route::delete('/perfil/fotos/{photo}', [\Modules\MemberPanel\App\Http\Controllers\ProfileController::class, 'deletePhoto'])->name('profile.photo.destroy');

        // Vínculos familiares — listar convites pendentes, solicitar novo vínculo, aceitar/recusar
        Route::get('/vinculos', [\Modules\MemberPanel\App\Http\Controllers\RelationshipController::class, 'pending'])->name('relationships.pending');
        Route::get('/vinculos/criar', [\Modules\MemberPanel\App\Http\Controllers\RelationshipController::class, 'create'])->name('relationships.create');
        Route::post('/vinculos', [\Modules\MemberPanel\App\Http\Controllers\RelationshipController::class, 'store'])->name('relationships.store');
        Route::get('/vinculos/buscar-cpf', [\Modules\MemberPanel\App\Http\Controllers\RelationshipController::class, 'searchMemberByCpf'])->name('relationships.search-cpf');
        Route::post('/vinculos/{user_relationship}/aceitar', [\Modules\MemberPanel\App\Http\Controllers\RelationshipController::class, 'accept'])->name('relationships.accept');
        Route::post('/vinculos/{user_relationship}/recusar', [\Modules\MemberPanel\App\Http\Controllers\RelationshipController::class, 'reject'])->name('relationships.reject');

        // Ministérios
        Route::get('/ministerios', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'index'])->name('ministries.index');
        Route::get('/ministerios/{ministry}', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'show'])->name('ministries.show');
        Route::post('/ministerios/{ministry}/join', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'join'])->name('ministries.join');
        Route::post('/ministerios/{ministry}/leave', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'leave'])->name('ministries.leave');
        Route::post('/ministerios/{ministry}/solicitacoes/{user}/aceitar', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'acceptAndSubmitToCouncil'])->name('ministries.requests.accept');
        Route::post('/ministerios/{ministry}/solicitacoes/{user}/recusar', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'rejectRequest'])->name('ministries.requests.reject');
        Route::get('/ministerios/{ministry}/reservas/criar', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'createReservation'])->name('ministries.reservations.create');
        Route::post('/ministerios/{ministry}/reservas', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'storeReservation'])->name('ministries.reservations.store');
        Route::get('/ministerios/{ministry}/relatorios/criar', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'createReport'])->name('ministries.reports.create');
        Route::post('/ministerios/{ministry}/relatorios', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'storeReport'])->name('ministries.reports.store');
        Route::get('/ministerios/{ministry}/relatorios/{report}/editar', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'editReport'])->name('ministries.reports.edit');
        Route::put('/ministerios/{ministry}/relatorios/{report}', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'updateReport'])->name('ministries.reports.update');

        // CBAV Bot (Gamification) — Elias: chat para dúvidas (EBD player usa com lesson_id)
        Route::get('/cbav-bot/analise', [\Modules\Gamification\App\Http\Controllers\CbavBotController::class, 'analysis'])->name('cbav-bot.analysis');
        Route::post('/cbav-bot/chat', [\Modules\Gamification\App\Http\Controllers\CbavBotController::class, 'chat'])->name('cbav-bot.chat');
        Route::post('/cbav-bot/dismiss', [\Modules\Gamification\App\Http\Controllers\CbavBotController::class, 'dismiss'])->name('cbav-bot.dismiss');

        // Notificações
        Route::get('/notificacoes', [\Modules\MemberPanel\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notificacoes/{notification}/read', [\Modules\MemberPanel\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notificacoes/read-all', [\Modules\MemberPanel\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::delete('/notificacoes/clear-all', [\Modules\MemberPanel\App\Http\Controllers\NotificationController::class, 'clearAll'])->name('notifications.clear-all');
        Route::delete('/notificacoes/{notification}', [\Modules\MemberPanel\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');

        // Preferências de notificações (Notifications Engine v2)
        Route::get('/preferencias/notificacoes', [\Modules\Notifications\App\Http\Controllers\MemberPanel\NotificationPreferencesController::class, 'index'])->name('preferences.notifications.index');
        Route::put('/preferencias/notificacoes', [\Modules\Notifications\App\Http\Controllers\MemberPanel\NotificationPreferencesController::class, 'update'])->name('preferences.notifications.update');

        // Bíblia (rotas específicas primeiro para evitar conflito com {version?})
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

        // Tesouraria (proxy no painel)
        Route::prefix('tesouraria')->name('treasury.')->group(function () {
            $treasuryController = \Modules\Treasury\App\Http\Controllers\MemberPanel\TreasuryController::class;
            Route::get('/', [$treasuryController, 'dashboard'])->name('dashboard');
            Route::get('/dashboard', [$treasuryController, 'dashboard'])->name('dashboard.index');
            Route::get('/transparencia', [$treasuryController, 'transparency'])->name('transparency');
            Route::get('/entradas', [$treasuryController, 'entriesIndex'])->name('entries.index');
            Route::get('/entradas/criar', [$treasuryController, 'entriesCreate'])->name('entries.create');
            Route::post('/entradas', [$treasuryController, 'entriesStore'])->name('entries.store');
            Route::get('/entradas/{entry}/editar', [$treasuryController, 'entriesEdit'])->name('entries.edit');
            Route::put('/entradas/{entry}', [$treasuryController, 'entriesUpdate'])->name('entries.update');
            Route::delete('/entradas/{entry}', [$treasuryController, 'entriesDestroy'])->name('entries.destroy');
            Route::post('/entradas/importar', [$treasuryController, 'proxy'])->defaults('controller', 'entries')->defaults('method', 'importPayment')->name('import');
            Route::get('/campanhas', [$treasuryController, 'campaignsIndex'])->name('campaigns.index');
            Route::get('/campanhas/criar', [$treasuryController, 'campaignsCreate'])->name('campaigns.create');
            Route::post('/campanhas', [$treasuryController, 'campaignsStore'])->name('campaigns.store');
            Route::get('/campanhas/{campaign}', [$treasuryController, 'campaignsShow'])->name('campaigns.show');
            Route::get('/campanhas/{campaign}/editar', [$treasuryController, 'campaignsEdit'])->name('campaigns.edit');
            Route::put('/campanhas/{campaign}', [$treasuryController, 'campaignsUpdate'])->name('campaigns.update');
            Route::delete('/campanhas/{campaign}', [$treasuryController, 'campaignsDestroy'])->name('campaigns.destroy');
            Route::get('/metas', [$treasuryController, 'goalsIndex'])->name('goals.index');
            Route::get('/metas/criar', [$treasuryController, 'goalsCreate'])->name('goals.create');
            Route::post('/metas', [$treasuryController, 'goalsStore'])->name('goals.store');
            Route::get('/metas/{goal}', [$treasuryController, 'goalsShow'])->name('goals.show');
            Route::get('/metas/{goal}/editar', [$treasuryController, 'goalsEdit'])->name('goals.edit');
            Route::put('/metas/{goal}', [$treasuryController, 'goalsUpdate'])->name('goals.update');
            Route::delete('/metas/{goal}', [$treasuryController, 'goalsDestroy'])->name('goals.destroy');
            Route::get('/relatorios', [$treasuryController, 'reportsIndex'])->name('reports.index');
            Route::get('/relatorios/exportar/excel', [$treasuryController, 'reportsExportExcel'])->name('reports.export.excel');
            Route::get('/relatorios/exportar/pdf', [$treasuryController, 'reportsExportPdf'])->name('reports.export.pdf');
            Route::get('/relatorios/exportar', [$treasuryController, 'reportsExport'])->name('reports.export');
            Route::middleware(['admin'])->group(function () use ($treasuryController) {
                Route::get('/permissoes', [$treasuryController, 'permissionsIndex'])->name('permissions.index');
                Route::get('/permissoes/criar', [$treasuryController, 'permissionsCreate'])->name('permissions.create');
                Route::post('/permissoes', [$treasuryController, 'permissionsStore'])->name('permissions.store');
                Route::get('/permissoes/{treasuryPermission}/editar', [$treasuryController, 'permissionsEdit'])->name('permissions.edit');
                Route::put('/permissoes/{treasuryPermission}', [$treasuryController, 'permissionsUpdate'])->name('permissions.update');
                Route::delete('/permissoes/{treasuryPermission}', [$treasuryController, 'permissionsDestroy'])->name('permissions.destroy');
            });
        });

        // PaymentGateway - Doações
        Route::get('/minhas-doacoes', [\Modules\PaymentGateway\App\Http\Controllers\MemberPanel\DonationController::class, 'index'])->name('donations.index');
        Route::get('/doacoes', [\Modules\PaymentGateway\App\Http\Controllers\MemberPanel\DonationController::class, 'create'])->name('donations.create');
        Route::post('/doacoes', [\Modules\PaymentGateway\App\Http\Controllers\MemberPanel\DonationController::class, 'store'])->name('donations.store');
        Route::get('/doacoes/{transactionId}', [\Modules\PaymentGateway\App\Http\Controllers\MemberPanel\DonationController::class, 'show'])->name('donations.show');
        Route::get('/doacoes/{transactionId}/retry', [\Modules\PaymentGateway\App\Http\Controllers\MemberPanel\DonationController::class, 'retry'])->name('donations.retry');
        Route::post('/doacoes/{transactionId}/retry', [\Modules\PaymentGateway\App\Http\Controllers\MemberPanel\DonationController::class, 'updateGateway'])->name('donations.update-gateway');
        Route::get('/doacoes/{transactionId}/status', [\Modules\PaymentGateway\App\Http\Controllers\MemberPanel\DonationController::class, 'checkStatus'])->name('donations.check-status');
    });

    // =====================================================================
    // Marketplace - Minhas Compras (painel/loja/pedidos)
    // =====================================================================
    Route::prefix('painel')->name('memberpanel.')->group(function () {
        Route::get('/loja/pedidos', [\Modules\Marketplace\Http\Controllers\MemberPanel\OrderController::class, 'index'])->name('marketplace.orders.index');
        Route::get('/loja/pedidos/{uuid}', [\Modules\Marketplace\Http\Controllers\MemberPanel\OrderController::class, 'show'])->name('marketplace.orders.show');
    });

    // =====================================================================
    // Events - Inscrições e eventos (painel/eventos)
    // =====================================================================
    Route::prefix('painel/eventos')->name('memberpanel.events.')->middleware(['auth', 'verified'])->group(function () {
        Route::get('/minhas-inscricoes', [\Modules\Events\App\Http\Controllers\MemberPanel\EventController::class, 'myRegistrations'])->name('my-registrations');
        Route::get('/minhas-inscricoes/{registration}', [\Modules\Events\App\Http\Controllers\MemberPanel\EventController::class, 'showRegistration'])->name('show-registration');
        Route::get('/', [\Modules\Events\App\Http\Controllers\MemberPanel\EventController::class, 'index'])->name('index');
        Route::get('/{event:slug}', [\Modules\Events\App\Http\Controllers\MemberPanel\EventController::class, 'show'])->name('show');
        Route::post('/{event:slug}/register', [\Modules\Events\App\Http\Controllers\MemberPanel\EventController::class, 'register'])->name('register');
        Route::get('/registration/{registration}/retry', [\Modules\Events\App\Http\Controllers\MemberPanel\EventController::class, 'retryRegistration'])->name('registration.retry');
        Route::post('/registration/{registration}/retry', [\Modules\Events\App\Http\Controllers\MemberPanel\EventController::class, 'updateRegistrationGateway'])->name('registration.update-gateway');
        Route::get('/registration/{registration}/pending', function ($registration) {
            $registration = \Modules\Events\App\Models\EventRegistration::findOrFail($registration);
            if ($registration->user_id !== auth()->id()) {
                abort(403);
            }
            return view('events::memberpanel.registration.pending', compact('registration'));
        })->name('registration.pending');
        Route::get('/registration/{registration}/confirmed', function ($registration) {
            $registration = \Modules\Events\App\Models\EventRegistration::with(['event', 'participants'])->findOrFail($registration);
            if ($registration->user_id !== auth()->id()) {
                abort(403);
            }
            return view('events::memberpanel.registration.confirmed', compact('registration'));
        })->name('registration.confirmed');
    });

    // =====================================================================
    // EBD - Plataforma EAD Cristã (painel/ebd)
    // =====================================================================
    Route::prefix('painel/ebd')->name('memberpanel.ebd.')->group(function () {
        // Portal do Aluno
        Route::prefix('aluno')->name('student.')->group(function () {
            Route::get('/', [\Modules\EBD\App\Http\Controllers\MemberPanel\StudentPanelController::class, 'index'])->name('index');
            Route::get('/turmas', [\Modules\EBD\App\Http\Controllers\MemberPanel\StudentPanelController::class, 'myClasses'])->name('my-classes');
            Route::get('/progresso', [\Modules\EBD\App\Http\Controllers\MemberPanel\StudentPanelController::class, 'myProgress'])->name('my-progress');
            Route::get('/licoes', [\Modules\EBD\App\Http\Controllers\MemberPanel\StudentPanelController::class, 'lessons'])->name('lessons');
            Route::get('/licoes/{lesson}', [\Modules\EBD\App\Http\Controllers\MemberPanel\StudentPanelController::class, 'showLesson'])->name('lessons.show');

            // LMS Player unificado sob o aluno
            Route::get('/aula/{lesson}', [\Modules\EBD\App\Http\Controllers\MemberPanel\ClassroomController::class, 'show'])->name('classroom.player');
            Route::post('/aula/{lesson}/concluir', [\Modules\EBD\App\Http\Controllers\MemberPanel\ClassroomController::class, 'markAsViewed'])->name('classroom.complete');
            Route::post('/aula/{lesson}/anotacoes', [\Modules\EBD\App\Http\Controllers\MemberPanel\ClassroomController::class, 'updateNotes'])->name('classroom.notes');
            Route::post('/avaliacoes/{evaluation}/enviar', [\Modules\EBD\App\Http\Controllers\MemberPanel\StudentPanelController::class, 'completeEvaluation'])->name('evaluations.complete');
        });

        // Portal do Professor
        Route::prefix('professor')->name('teacher.')->group(function () {
            Route::get('/', [\Modules\EBD\App\Http\Controllers\MemberPanel\TeacherPanelController::class, 'index'])->name('index');
            Route::get('/turmas', [\Modules\EBD\App\Http\Controllers\MemberPanel\TeacherPanelController::class, 'myClasses'])->name('my-classes');
            Route::get('/turmas/{class}', [\Modules\EBD\App\Http\Controllers\MemberPanel\TeacherPanelController::class, 'classStudents'])->name('classes.show');
            Route::get('/licoes', [\Modules\EBD\App\Http\Controllers\MemberPanel\TeacherPanelController::class, 'lessons'])->name('lessons');
            Route::get('/licoes/{lesson}', [\Modules\EBD\App\Http\Controllers\MemberPanel\TeacherPanelController::class, 'showLesson'])->name('lessons.show');
            Route::get('/licoes/{lesson}/presenca', [\Modules\EBD\App\Http\Controllers\MemberPanel\TeacherPanelController::class, 'manageAttendance'])->name('attendance.manage');
            Route::post('/licoes/{lesson}/presenca', [\Modules\EBD\App\Http\Controllers\MemberPanel\TeacherPanelController::class, 'storeAttendance'])->name('attendance.store');
            Route::get('/avaliacoes', [\Modules\EBD\App\Http\Controllers\MemberPanel\TeacherPanelController::class, 'evaluations'])->name('evaluations');
            Route::get('/avaliacoes/{evaluation}/corrigir', [\Modules\EBD\App\Http\Controllers\MemberPanel\TeacherPanelController::class, 'gradeEvaluation'])->name('evaluations.grade');
            Route::post('/avaliacoes/{evaluation}/corrigir', [\Modules\EBD\App\Http\Controllers\MemberPanel\TeacherPanelController::class, 'storeGrade'])->name('evaluations.grade.store');
        });

        // Arcade & Quiz
        Route::prefix('arcade')->name('arcade.')->group(function () {
            Route::get('/', [\Modules\EBD\App\Http\Controllers\MemberPanel\ArcadeController::class, 'index'])->name('index');
            Route::get('/leaderboard', [\Modules\EBD\App\Http\Controllers\MemberPanel\ArcadeController::class, 'leaderboard'])->name('leaderboard');
            Route::get('/versemaster', [\Modules\EBD\App\Http\Controllers\MemberPanel\ArcadeController::class, 'versemaster'])->name('versemaster');
            Route::get('/get-verse', [\Modules\EBD\App\Http\Controllers\MemberPanel\ArcadeController::class, 'getVerse'])->name('get-verse');
            Route::get('/quiz', [\Modules\EBD\App\Http\Controllers\MemberPanel\ArcadeController::class, 'quiz'])->name('quiz');
            Route::get('/quiz-data', [\Modules\EBD\App\Http\Controllers\MemberPanel\ArcadeController::class, 'getQuizData'])->name('quiz.data');
            Route::get('/memory', [\Modules\EBD\App\Http\Controllers\MemberPanel\ArcadeController::class, 'memory'])->name('memory');
            Route::get('/who-said-it', [\Modules\EBD\App\Http\Controllers\MemberPanel\ArcadeController::class, 'whoSaidIt'])->name('whosaidit');
            Route::get('/timeline', [\Modules\EBD\App\Http\Controllers\MemberPanel\ArcadeController::class, 'timeline'])->name('timeline');
            Route::get('/sword', [\Modules\EBD\App\Http\Controllers\MemberPanel\ArcadeController::class, 'sword'])->name('sword');
            Route::get('/hangman', [\Modules\EBD\App\Http\Controllers\MemberPanel\ArcadeController::class, 'hangman'])->name('hangman');
            Route::get('/wordsearch', [\Modules\EBD\App\Http\Controllers\MemberPanel\ArcadeController::class, 'wordsearch'])->name('wordsearch');
            Route::get('/hero', [\Modules\EBD\App\Http\Controllers\MemberPanel\ArcadeController::class, 'hero'])->name('hero');
            Route::get('/fillblank', [\Modules\EBD\App\Http\Controllers\MemberPanel\ArcadeController::class, 'fillblank'])->name('fillblank');
            Route::get('/bookchallenge', [\Modules\EBD\App\Http\Controllers\MemberPanel\ArcadeController::class, 'bookchallenge'])->name('bookchallenge');
            Route::get('/trio', [\Modules\EBD\App\Http\Controllers\MemberPanel\ArcadeController::class, 'trio'])->name('trio');
            Route::get('/crossword', [\Modules\EBD\App\Http\Controllers\MemberPanel\ArcadeController::class, 'crossword'])->name('crossword');
            Route::get('/parables', [\Modules\EBD\App\Http\Controllers\MemberPanel\ArcadeController::class, 'parables'])->name('parables');
            Route::get('/navigator', [\Modules\EBD\App\Http\Controllers\MemberPanel\ArcadeController::class, 'navigator'])->name('navigator');
            Route::post('/submit/{game}', [\Modules\EBD\App\Http\Controllers\MemberPanel\ArcadeController::class, 'submitScore'])->name('submit');
        });

        Route::get('/quiz/sessao/{session}', [\Modules\EBD\App\Http\Controllers\MemberPanel\QuizController::class, 'showClient'])->name('quiz.client');
    });

    // =====================================================================
    // Sermons - Sermões, Séries, Estudos, Comentários (painel/sermoes, etc.)
    // =====================================================================
    Route::prefix('painel/sermoes')->name('memberpanel.sermons.')->group(function () {
        Route::get('/', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'index'])->name('index');
        Route::get('/meus-sermoes', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'mySermons'])->name('my-sermons');
        Route::get('/meus-favoritos', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'myFavorites'])->name('my-favorites');
        Route::get('/criar', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'create'])->name('create');
        Route::post('/', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'store'])->name('store');
        Route::get('/convite/{collaborator}', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'showCollaboratorInvite'])->name('collaborator.invite');
        Route::post('/convite/{collaborator}/respond', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'respondCollaborator'])->name('collaborator.respond');
        Route::get('/{sermon}/export-pdf', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/{sermon}/editar', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'edit'])->name('edit');
        Route::put('/{sermon}', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'update'])->name('update');
        Route::delete('/{sermon}', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'destroy'])->name('destroy');
        Route::get('/{sermon}', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'show'])->name('show');
        Route::post('/{sermon}/favoritar', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'toggleFavorite'])->name('toggle-favorite');
        Route::post('/{sermon}/comentar', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'storeComment'])->name('store-comment');
    });
    Route::prefix('painel/series')->name('memberpanel.series.')->group(function () {
        Route::get('/', [\Modules\Sermons\App\Http\Controllers\MemberPanel\BibleSeriesController::class, 'index'])->name('index');
        Route::get('/{series}', [\Modules\Sermons\App\Http\Controllers\MemberPanel\BibleSeriesController::class, 'show'])->name('show');
    });
    Route::prefix('painel/estudos')->name('memberpanel.studies.')->group(function () {
        Route::get('/', [\Modules\Sermons\App\Http\Controllers\MemberPanel\BibleStudyController::class, 'index'])->name('index');
        Route::get('/{study}', [\Modules\Sermons\App\Http\Controllers\MemberPanel\BibleStudyController::class, 'show'])->name('show');
    });
    Route::prefix('painel/comentarios')->name('memberpanel.commentaries.')->group(function () {
        Route::get('/', [\Modules\Sermons\App\Http\Controllers\MemberPanel\BibleCommentaryController::class, 'index'])->name('index');
        Route::get('/{commentary}', [\Modules\Sermons\App\Http\Controllers\MemberPanel\BibleCommentaryController::class, 'show'])->name('show');
    });

    // =====================================================================
    // ChurchCouncil - Conselho (painel/conselho)
    // =====================================================================
    Route::prefix('painel/conselho')->name('memberpanel.churchcouncil.')->middleware(['auth'])->group(function () {
        Route::get('/', [\Modules\ChurchCouncil\App\Http\Controllers\MemberPanel\CouncilController::class, 'index'])->name('index');
        Route::prefix('reunioes')->name('meetings.')->group(function () {
            Route::get('/', [\Modules\ChurchCouncil\App\Http\Controllers\MemberPanel\CouncilController::class, 'meetings'])->name('index');
            Route::get('/{meeting}', [\Modules\ChurchCouncil\App\Http\Controllers\MemberPanel\CouncilController::class, 'showMeeting'])->name('show');
            Route::post('/pautas/{agenda}/votar', [\Modules\ChurchCouncil\App\Http\Controllers\MemberPanel\CouncilController::class, 'castVote'])->name('vote');
        });
        Route::prefix('pautas')->name('agendas.')->group(function () {
            Route::get('/', [\Modules\ChurchCouncil\App\Http\Controllers\MemberPanel\CouncilController::class, 'agendas'])->name('index');
            Route::get('/criar', [\Modules\ChurchCouncil\App\Http\Controllers\MemberPanel\CouncilController::class, 'createAgenda'])->name('create');
            Route::post('/', [\Modules\ChurchCouncil\App\Http\Controllers\MemberPanel\CouncilController::class, 'storeAgenda'])->name('store');
        });
        Route::prefix('aprovacoes')->name('approvals.')->group(function () {
            Route::get('/', [\Modules\ChurchCouncil\App\Http\Controllers\MemberPanel\CouncilController::class, 'approvals'])->name('index');
            Route::get('/pendentes', [\Modules\ChurchCouncil\App\Http\Controllers\MemberPanel\CouncilController::class, 'pendingApprovals'])->name('pending');
            Route::get('/{approval}', [\Modules\ChurchCouncil\App\Http\Controllers\MemberPanel\CouncilController::class, 'showApproval'])->name('show');
        });
        Route::prefix('perfil')->name('profile.')->group(function () {
            Route::get('/', [\Modules\ChurchCouncil\App\Http\Controllers\MemberPanel\CouncilController::class, 'profile'])->name('index');
            Route::post('/', [\Modules\ChurchCouncil\App\Http\Controllers\MemberPanel\CouncilController::class, 'updateProfile'])->name('update');
        });
        Route::post('/solicitar-aprovacao', [\Modules\ChurchCouncil\App\Http\Controllers\MemberPanel\CouncilController::class, 'submitApprovalRequest'])->name('submit-approval');
        Route::get('/documentos', [\Modules\ChurchCouncil\App\Http\Controllers\MemberPanel\CouncilController::class, 'documents'])->name('documents.index');
        Route::get('/documentos/{document}/baixar', [\Modules\ChurchCouncil\App\Http\Controllers\MemberPanel\CouncilController::class, 'downloadDocument'])->name('documents.download');
        Route::prefix('projetos')->name('projects.')->group(function () {
            Route::get('/', [\Modules\ChurchCouncil\App\Http\Controllers\MemberPanel\CouncilController::class, 'projects'])->name('index');
            Route::get('/criar', [\Modules\ChurchCouncil\App\Http\Controllers\MemberPanel\CouncilController::class, 'createProject'])->name('create');
            Route::post('/', [\Modules\ChurchCouncil\App\Http\Controllers\MemberPanel\CouncilController::class, 'storeProject'])->name('store');
            Route::get('/{project}', [\Modules\ChurchCouncil\App\Http\Controllers\MemberPanel\CouncilController::class, 'showProject'])->name('show');
        });
    });

    // =====================================================================
    // Intercessor (painel/intercessor) - nome: member.intercessor.*
    // =====================================================================
    Route::middleware([\Modules\Intercessor\App\Http\Middleware\CheckIntercessorStatus::class])->prefix('painel/intercessor')->name('member.intercessor.')->group(function () {
        Route::get('/', [\Modules\Intercessor\App\Http\Controllers\MemberPanel\IntercessorDashboardController::class, 'index'])->name('dashboard');
        Route::resource('requests', \Modules\Intercessor\App\Http\Controllers\MemberPanel\IntercessorController::class);
        Route::get('/mural-de-intercessao', [\Modules\Intercessor\App\Http\Controllers\MemberPanel\PrayerRoomController::class, 'index'])->name('room.index');
        Route::get('/mural-de-testemunhos', [\Modules\Intercessor\App\Http\Controllers\MemberPanel\PrayerRoomController::class, 'testimonies'])->name('room.testimonies');
        Route::get('/sala-de-guerra/{request}', [\Modules\Intercessor\App\Http\Controllers\MemberPanel\PrayerRoomController::class, 'show'])->name('room.show');
        Route::post('/sala-de-guerra/{prayerRequest}/orar', [\Modules\Intercessor\App\Http\Controllers\MemberPanel\PrayerRoomController::class, 'commit'])->name('room.commit');
        Route::post('/sala-de-guerra/{prayerRequest}/concluir', [\Modules\Intercessor\App\Http\Controllers\MemberPanel\PrayerRoomController::class, 'finish'])->name('room.finish');
        Route::post('/sala-de-guerra/{prayerRequest}/interagir', [\Modules\Intercessor\App\Http\Controllers\MemberPanel\PrayerRoomController::class, 'interact'])->name('room.interact');
        Route::get('/meus-pedidos/{request}/testemunhar', [\Modules\Intercessor\App\Http\Controllers\MemberPanel\IntercessorController::class, 'testimony'])->name('requests.testimony');
        Route::post('/meus-pedidos/{request}/testemunhar', [\Modules\Intercessor\App\Http\Controllers\MemberPanel\IntercessorController::class, 'submitTestimony'])->name('requests.testimony.store');
    });

    // =====================================================================
    // Worship - Louvor (painel/louvor) - nome: worship.member.*
    // =====================================================================
    Route::prefix('painel/louvor')->name('worship.member.')->group(function () {
        Route::get('tocar/{setlist}', [\Modules\Worship\App\Http\Controllers\MemberPanel\MusicianStageController::class, 'view'])->name('stage.view');
        Route::get('minhas-escalas', [\Modules\Worship\App\Http\Controllers\MemberPanel\MyRosterController::class, 'index'])->name('rosters.index');
        Route::post('minhas-escalas/{roster}/status', [\Modules\Worship\App\Http\Controllers\MemberPanel\MyRosterController::class, 'updateStatus'])->name('rosters.status');
        Route::get('rehearsal', [\Modules\Worship\App\Http\Controllers\MemberPanel\RehearsalRoomController::class, 'index'])->name('rehearsal.index');
        Route::get('rehearsal/{setlist}', [\Modules\Worship\App\Http\Controllers\MemberPanel\RehearsalRoomController::class, 'show'])->name('rehearsal.show');
        Route::get('academy', [\Modules\Worship\App\Http\Controllers\MemberPanel\AcademyMemberController::class, 'index'])->name('academy.index');
        Route::get('academy/course/{id}/classroom', [\Modules\Worship\App\Http\Controllers\MemberPanel\AcademyMemberController::class, 'classroom'])->name('academy.classroom');
        Route::get('academy/course/{id}', function ($id) {
            return redirect()->route('worship.member.academy.classroom', $id);
        })->name('academy.course');
    });

    // =====================================================================
    // Projection - Projeção (painel/projection) - nome: memberpanel.projection.*
    // =====================================================================
    Route::prefix('painel')->name('memberpanel.')->group(function () {
        Route::get('projection', [\Modules\Projection\App\Http\Controllers\ProjectionController::class, 'memberIndex'])->name('projection.index');
        Route::get('projection/console/{setlist?}', [\Modules\Projection\App\Http\Controllers\ProjectionController::class, 'memberConsole'])->name('projection.console');
        Route::get('projection/screen', [\Modules\Projection\App\Http\Controllers\ProjectionController::class, 'memberScreen'])->name('projection.screen');
        Route::get('projection/remote', [\Modules\Projection\App\Http\Controllers\ProjectionController::class, 'memberRemote'])->name('projection.remote');
    });

    // =====================================================================
    // Transfer Letters (painel/transferencias) - memberpanel.transfers.*
    // =====================================================================
    Route::prefix('painel/transferencias')->name('memberpanel.transfers.')->middleware(['auth'])->group(function () {
        Route::get('/', [\Modules\MemberPanel\App\Http\Controllers\TransferController::class, 'index'])->name('index');
        Route::get('/solicitar', [\Modules\MemberPanel\App\Http\Controllers\TransferController::class, 'create'])->name('create');
        Route::post('/', [\Modules\MemberPanel\App\Http\Controllers\TransferController::class, 'store'])->name('store');
    });
});
