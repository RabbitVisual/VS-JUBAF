<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas do Gabinete Pastoral (Centralizadas)
|--------------------------------------------------------------------------
|
| Todas as rotas destinadas ao painel do pastor (prefixo /pastor).
| Middleware: auth, verified, pastor. Ambiente ministerial, sem
| configurações técnicas (módulos, gateways, logs).
|
*/

Route::middleware(['auth', 'verified', 'pastor'])->prefix('pastor')->name('pastor.')->group(function () {

    // =====================================================================
    // PastoralPanel (Core): Dashboard
    // =====================================================================
    Route::get('/', [\Modules\PastoralPanel\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [\Modules\PastoralPanel\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index');

    // =====================================================================
    // Rebanho (Membros) - visualização pastoral
    // =====================================================================
    Route::prefix('rebanho')->name('rebanho.')->group(function () {
        Route::get('/', [\Modules\PastoralPanel\App\Http\Controllers\RebanhoController::class, 'index'])->name('index');
        Route::get('/criar', [\Modules\Admin\App\Http\Controllers\UserController::class, 'create'])->name('create');
        Route::post('/', [\Modules\Admin\App\Http\Controllers\UserController::class, 'store'])->name('store');
        Route::get('/{user}', [\Modules\PastoralPanel\App\Http\Controllers\RebanhoController::class, 'show'])->name('show');
        Route::get('/{user}/editar', [\Modules\Admin\App\Http\Controllers\UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [\Modules\Admin\App\Http\Controllers\UserController::class, 'update'])->name('update');
        Route::get('/{user}/arvore-familiar', [\Modules\Admin\App\Http\Controllers\UserController::class, 'familyTreeAnalysis'])->name('family-tree');
    });

    // =====================================================================
    // Oração (Intercessor) - pedidos de oração
    // =====================================================================
    Route::prefix('oracao')->name('oracao.')->group(function () {
        Route::get('/', [\Modules\PastoralPanel\App\Http\Controllers\OracaoController::class, 'index'])->name('index');
        Route::get('/{request}', [\Modules\PastoralPanel\App\Http\Controllers\OracaoController::class, 'show'])->name('show')->where('request', '[0-9]+');
        Route::post('/{request}/marcar-orado', [\Modules\PastoralPanel\App\Http\Controllers\OracaoController::class, 'markAsPrayed'])->name('marcar-orado')->where('request', '[0-9]+');
    });

    // =====================================================================
    // Sermões (Estúdio da Palavra) - CRUD completo, layout pastoral
    // =====================================================================
    Route::prefix('sermoes')->name('sermoes.')->group(function () {
        Route::get('/', function () {
            return redirect()->route('pastor.sermoes.sermons.index');
        })->name('index');
        Route::get('sermons/{sermon}/export-pdf', [\Modules\Sermons\App\Http\Controllers\Pastoral\SermonController::class, 'exportPdf'])->name('sermons.export-pdf');
        Route::post('sermons/{sermon}/collaborators', [\Modules\Sermons\App\Http\Controllers\Pastoral\SermonController::class, 'inviteCollaborator'])->name('sermons.collaborators.invite');
        Route::resource('sermons', \Modules\Sermons\App\Http\Controllers\Pastoral\SermonController::class);
        Route::resource('categories', \Modules\Sermons\App\Http\Controllers\Pastoral\CategoryController::class)->except(['show']);
        Route::resource('series', \Modules\Sermons\App\Http\Controllers\Pastoral\BibleSeriesController::class)->except(['show']);
        Route::resource('studies', \Modules\Sermons\App\Http\Controllers\Pastoral\BibleStudyController::class)->except(['show']);
        Route::resource('commentaries', \Modules\Sermons\App\Http\Controllers\Pastoral\BibleCommentaryController::class)->except(['show']);
    });

    // =====================================================================
    // Educação (EBD + Worship Academy) - layout pastoral
    // =====================================================================
    Route::prefix('educacao')->name('educacao.')->group(function () {
        Route::get('/', [\Modules\PastoralPanel\App\Http\Controllers\EducacaoController::class, 'index'])->name('index');
    });

    // =====================================================================
    // EBD (Escola Bíblica Dominical) - visão pastoral completa
    // =====================================================================
    Route::prefix('ebd')->name('ebd.')->group(function () {
        $dash = \Modules\EBD\App\Http\Controllers\Pastoral\DashboardController::class;
        $classCtrl = \Modules\EBD\App\Http\Controllers\Pastoral\ClassController::class;
        $courseCtrl = \Modules\EBD\App\Http\Controllers\Pastoral\CourseController::class;
        $lessonCtrl = \Modules\EBD\App\Http\Controllers\Pastoral\LessonController::class;
        $teacherCtrl = \Modules\EBD\App\Http\Controllers\Pastoral\TeacherController::class;
        $studentCtrl = \Modules\EBD\App\Http\Controllers\Pastoral\StudentController::class;
        $attendanceCtrl = \Modules\EBD\App\Http\Controllers\Pastoral\AttendanceController::class;
        $evaluationCtrl = \Modules\EBD\App\Http\Controllers\Pastoral\EvaluationController::class;

        Route::get('/', [$dash, 'index'])->name('dashboard');
        Route::get('/dashboard', [$dash, 'index'])->name('dashboard.index');
        Route::get('/attendance', [$attendanceCtrl, 'index'])->name('attendance.index');
        Route::get('/attendance/lesson/{lesson}', [$attendanceCtrl, 'show'])->name('attendance.show');
        Route::get('/evaluations', [$evaluationCtrl, 'index'])->name('evaluations.index');
        Route::get('/evaluations/{evaluation}', [$evaluationCtrl, 'show'])->name('evaluations.show');
        Route::resource('classes', $classCtrl)->only(['index', 'show']);
        Route::resource('courses', $courseCtrl)->only(['index', 'show']);
        Route::resource('lessons', $lessonCtrl)->only(['index', 'show']);
        Route::get('/teachers', [$teacherCtrl, 'index'])->name('teachers.index');
        Route::get('/students', [$studentCtrl, 'index'])->name('students.index');
    });

    // =====================================================================
    // Ministérios (visão pastoral: listar, ver, aprovar membros, planos, relatório)
    // =====================================================================
    Route::prefix('ministerios')->name('ministerios.')->group(function () {
        Route::get('/', [\Modules\Ministries\App\Http\Controllers\Pastoral\MinistryController::class, 'index'])->name('index');
        Route::get('/plans/lista', [\Modules\Ministries\App\Http\Controllers\Pastoral\MinistryPlanController::class, 'index'])->name('plans.index');
        Route::get('/{ministry}/relatorio-consolidado', [\Modules\Ministries\App\Http\Controllers\Pastoral\MinistryReportController::class, 'exportConsolidated'])->name('reports.consolidated');
        Route::post('/{ministry}/membros/{user}/aprovar', [\Modules\Ministries\App\Http\Controllers\Pastoral\MinistryController::class, 'approveMember'])->name('members.approve');
        Route::get('/{ministry}/plans/{plan}', [\Modules\Ministries\App\Http\Controllers\Pastoral\MinistryPlanController::class, 'show'])->name('plans.show');
        Route::get('/{ministry}', [\Modules\Ministries\App\Http\Controllers\Pastoral\MinistryController::class, 'show'])->name('show');
    });

    // =====================================================================
    // Transparência (visão geral) e Tesouraria (gerenciamento completo)
    // =====================================================================
    Route::prefix('transparencia')->name('transparencia.')->group(function () {
        Route::get('/', [\Modules\PastoralPanel\App\Http\Controllers\TransparenciaController::class, 'index'])->name('index');
    });

    Route::prefix('tesouraria')->name('tesouraria.')->group(function () {
        $dash = \Modules\Treasury\App\Http\Controllers\Pastoral\DashboardController::class;
        $entryCtrl = \Modules\Treasury\App\Http\Controllers\Pastoral\FinancialEntryController::class;
        $campaignCtrl = \Modules\Treasury\App\Http\Controllers\Pastoral\CampaignController::class;
        $goalCtrl = \Modules\Treasury\App\Http\Controllers\Pastoral\FinancialGoalController::class;
        $reportCtrl = \Modules\Treasury\App\Http\Controllers\Pastoral\ReportController::class;

        Route::get('/', [$dash, 'index'])->name('dashboard');
        Route::get('/dashboard', [$dash, 'index'])->name('dashboard.index');

        Route::get('/entries', [$entryCtrl, 'index'])->name('entries.index');
        Route::get('/entries/create', [$entryCtrl, 'create'])->name('entries.create');
        Route::post('/entries', [$entryCtrl, 'store'])->name('entries.store');
        Route::get('/entries/import/{payment}', [$entryCtrl, 'importPayment'])->name('entries.import');
        Route::get('/entries/{entry}/edit', [$entryCtrl, 'edit'])->name('entries.edit');
        Route::put('/entries/{entry}', [$entryCtrl, 'update'])->name('entries.update');
        Route::delete('/entries/{entry}', [$entryCtrl, 'destroy'])->name('entries.destroy');
        Route::post('/entries/{entry}/reverse', [$entryCtrl, 'reverse'])->name('entries.reverse');

        Route::get('/campaigns', [$campaignCtrl, 'index'])->name('campaigns.index');
        Route::get('/campaigns/create', [$campaignCtrl, 'create'])->name('campaigns.create');
        Route::post('/campaigns', [$campaignCtrl, 'store'])->name('campaigns.store');
        Route::get('/campaigns/{campaign}', [$campaignCtrl, 'show'])->name('campaigns.show');
        Route::get('/campaigns/{campaign}/edit', [$campaignCtrl, 'edit'])->name('campaigns.edit');
        Route::put('/campaigns/{campaign}', [$campaignCtrl, 'update'])->name('campaigns.update');
        Route::delete('/campaigns/{campaign}', [$campaignCtrl, 'destroy'])->name('campaigns.destroy');

        Route::get('/goals', [$goalCtrl, 'index'])->name('goals.index');
        Route::get('/goals/create', [$goalCtrl, 'create'])->name('goals.create');
        Route::post('/goals', [$goalCtrl, 'store'])->name('goals.store');
        Route::get('/goals/{goal}', [$goalCtrl, 'show'])->name('goals.show');
        Route::get('/goals/{goal}/edit', [$goalCtrl, 'edit'])->name('goals.edit');
        Route::put('/goals/{goal}', [$goalCtrl, 'update'])->name('goals.update');
        Route::delete('/goals/{goal}', [$goalCtrl, 'destroy'])->name('goals.destroy');

        Route::get('/reports', [$reportCtrl, 'index'])->name('reports.index');
        Route::get('/reports/export/excel', [$reportCtrl, 'exportExcel'])->name('reports.export.excel');
        Route::get('/reports/export/pdf', [$reportCtrl, 'exportPdf'])->name('reports.export.pdf');
    });

    // =====================================================================
    // Conselho (ChurchCouncil - layout pastoral)
    // =====================================================================
    $conselhoCtrl = \Modules\PastoralPanel\App\Http\Controllers\ConselhoController::class;
    Route::prefix('conselho')->name('conselho.')->group(function () use ($conselhoCtrl) {
        Route::get('/', [$conselhoCtrl, 'index'])->name('index');
        Route::get('/aprovacoes', [$conselhoCtrl, 'approvals'])->name('approvals');
        Route::get('/aprovacoes/{approval}', [$conselhoCtrl, 'showApproval'])->name('approvals.show');
        Route::post('/aprovacoes/{approval}/aprovar', [$conselhoCtrl, 'approve'])->name('approvals.approve');
        Route::post('/aprovacoes/{approval}/rejeitar', [$conselhoCtrl, 'reject'])->name('approvals.reject');
        Route::get('/reunioes', [$conselhoCtrl, 'meetings'])->name('meetings.index');
        Route::get('/reunioes/{meeting}', [$conselhoCtrl, 'showMeeting'])->name('meetings.show');
        Route::get('/reunioes/{meeting}/ata/pdf', [$conselhoCtrl, 'exportMinutesPdf'])->name('meetings.minutes-pdf');
        Route::get('/reunioes/{meeting}/convocacao/pdf', [$conselhoCtrl, 'exportConvocationPdf'])->name('meetings.convocation-pdf');
        Route::get('/pautas', [$conselhoCtrl, 'agendas'])->name('agendas.index');
        Route::get('/documentos', [$conselhoCtrl, 'documentsIndex'])->name('documents.index');
        Route::get('/documentos/{document}', [$conselhoCtrl, 'documentShow'])->name('documents.show');
        Route::get('/documentos/{document}/baixar', [$conselhoCtrl, 'documentDownload'])->name('documents.download');
        Route::get('/projetos', [$conselhoCtrl, 'projectsIndex'])->name('projects.index');
        Route::get('/projetos/{project}', [$conselhoCtrl, 'projectShow'])->name('projects.show');
        Route::get('/membros', [$conselhoCtrl, 'membersIndex'])->name('members.index');
    });

    // =====================================================================
    // Eventos (Events - painel pastoral completo)
    // =====================================================================
    $eventosCtrl = \Modules\PastoralPanel\App\Http\Controllers\EventosController::class;
    Route::prefix('eventos')->name('eventos.')->group(function () use ($eventosCtrl) {
        Route::get('/', [$eventosCtrl, 'index'])->name('index');
        Route::get('/check-in', [$eventosCtrl, 'checkinIndex'])->name('checkin.index');
        Route::post('/check-in/validar', [$eventosCtrl, 'checkinValidate'])->name('checkin.validate');
        Route::get('/{event}', [$eventosCtrl, 'show'])->name('show');
        Route::get('/{event}/inscricoes', [$eventosCtrl, 'registrationsIndex'])->name('registrations.index');
        Route::get('/{event}/inscricoes/exportar/pdf', [$eventosCtrl, 'exportPdf'])->name('registrations.export-pdf');
        Route::get('/{event}/inscricoes/exportar/crachas', [$eventosCtrl, 'exportBadges'])->name('registrations.export-badges');
        Route::get('/{event}/inscricoes/exportar/excel', [$eventosCtrl, 'exportExcel'])->name('registrations.export-excel');
        Route::get('/{event}/inscricoes/{registration}', [$eventosCtrl, 'registrationShow'])->name('registrations.show');
        Route::post('/{event}/inscricoes/{registration}/confirmar', [$eventosCtrl, 'confirmRegistration'])->name('registrations.confirm');
        Route::post('/{event}/inscricoes/{registration}/cancelar', [$eventosCtrl, 'cancelRegistration'])->name('registrations.cancel');
    });
});
