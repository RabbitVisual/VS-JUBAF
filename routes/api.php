<?php

use App\Http\Controllers\Api\CepController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas API (Centralizadas)
|--------------------------------------------------------------------------
|
| Todas as rotas de API da aplicação. O prefixo /api e o middleware
| 'api' são aplicados automaticamente pelo bootstrap.
| Organizado por recurso para manutenção e segurança.
|
*/

// =====================================================================
// CEP (público)
// =====================================================================
Route::prefix('cep')->name('cep.')->group(function () {
    Route::get('/buscar', [CepController::class, 'buscar'])->name('buscar');
    Route::get('/validar', [CepController::class, 'validar'])->name('validar');
    Route::get('/cidades/{uf}', [CepController::class, 'cidadesPorUf'])->name('cidades.uf');
    Route::get('/cidades', [CepController::class, 'cidadesPorNome'])->name('cidades.nome');
});

Route::prefix('cep-ranges')->name('cep-ranges.')->group(function () {
    Route::get('/locations', [\Modules\Admin\App\Http\Controllers\CepRangeController::class, 'getLocations'])->name('locations');
});

// =====================================================================
// Notificações API v1 – única API de notificações (web + auth, formato { data })
// Alimenta todo o sistema: painéis, polling, SPA. Sem rotas legadas.
// =====================================================================
$notificationsV1 = \Modules\Notifications\App\Http\Controllers\Api\V1\NotificationController::class;
Route::middleware(['web', 'auth'])->prefix('v1/notifications')->name('notifications.api.')->group(function () use ($notificationsV1) {
    Route::get('/', [$notificationsV1, 'index'])->name('index');
    Route::get('/unread-count', [$notificationsV1, 'unreadCount'])->name('unread-count');
    Route::post('/read-all', [$notificationsV1, 'markAllAsRead'])->name('read-all');
    Route::delete('/clear-all', [$notificationsV1, 'clearAll'])->name('clear-all');
    Route::post('/{userNotification}/read', [$notificationsV1, 'markAsRead'])->name('read');
    Route::delete('/{userNotification}', [$notificationsV1, 'destroy'])->name('destroy');
});

// =====================================================================
// Bible API v1 – API central única (pública, throttle, formato { data })
// =====================================================================
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

// =====================================================================
// Worship API v1 – setlists, músicas, slides ChordPro, Academy (formato { data })
// Alimenta Projection, Sermons, Admin e MemberPanel. Sem rotas legadas worship/api.
// =====================================================================
$worshipV1 = \Modules\Worship\App\Http\Controllers\Api\V1\WorshipController::class;
Route::middleware(['throttle:60,1'])->prefix('v1/worship')->name('worship.api.')->group(function () use ($worshipV1) {
    Route::get('setlists', [$worshipV1, 'setlists'])->name('setlists');
    Route::get('setlists/{id}', [$worshipV1, 'setlist'])->name('setlists.show');
    Route::get('songs', [$worshipV1, 'songs'])->name('songs.index');
    Route::get('songs/{id}', [$worshipV1, 'song'])->name('songs.show');
    Route::get('songs/{id}/slides', [$worshipV1, 'songSlides'])->name('songs.slides');
    Route::middleware(['web', 'auth'])->group(function () use ($worshipV1) {
        Route::get('academy/courses', [$worshipV1, 'academyCourses'])->name('academy.courses');
        Route::post('academy/courses', [$worshipV1, 'academyStore'])->name('academy.courses.store');
        Route::get('academy/courses/{id}', [$worshipV1, 'academyCourse'])->name('academy.courses.show');
        Route::get('academy/courses/{id}/structure', [$worshipV1, 'academyCourseStructure'])->name('academy.courses.structure');
        Route::post('academy/courses/{id}/structure', [$worshipV1, 'academyUpdateStructure'])->name('academy.courses.structure.update');
        Route::post('academy/lessons/{id}/complete', [$worshipV1, 'academyLessonComplete'])->name('academy.lessons.complete');
    });
});

// =====================================================================
// PaymentGateway API v1 – gateways ativos e status de pagamento (formato { data })
// =====================================================================
$paymentGatewayV1 = \Modules\PaymentGateway\App\Http\Controllers\Api\V1\PaymentGatewayController::class;
Route::middleware(['throttle:60,1'])->prefix('v1')->group(function () use ($paymentGatewayV1) {
    Route::get('payment-gateways', [$paymentGatewayV1, 'index'])->name('api.payment-gateways.index');
    Route::get('payments/status', [$paymentGatewayV1, 'paymentStatus'])->name('api.payments.status');
    Route::get('payments/{transactionId}/status', [$paymentGatewayV1, 'paymentStatus'])->name('api.payments.status.show');
});

// =====================================================================
// PaymentGateway – Webhook canônico (público para gateways; throttle anti-abuso)
// =====================================================================
Route::middleware(['throttle:120,1'])->prefix('v1/gateway')->name('api.')->group(function () {
    Route::post('/webhook/{driver}', [\Modules\PaymentGateway\App\Http\Controllers\GatewayWebhookController::class, 'handle'])->name('gateway.webhook');
});

if (config('app.debug')) {
    Route::get('/debug/simulate-payment/{driver}', [\Modules\PaymentGateway\App\Http\Controllers\DebugController::class, 'simulatePayment'])->name('debug.simulate-payment');
}

// =====================================================================
// Auth API v1 – desktop app login (throttle 10/min anti brute-force)
// =====================================================================
$authV1 = \App\Http\Controllers\Api\V1\AuthController::class;
Route::middleware(['throttle:10,1'])->prefix('v1/auth')->name('auth.api.')->group(function () use ($authV1) {
    Route::post('desktop-login', [$authV1, 'desktopLogin'])->name('desktop-login');
});

// =====================================================================
// Projection API v1 – state, assets, slides, timeline, lyrics (formato { data })
// Única API de projeção; console, screen e remote consomem apenas esta API.
// Serve de assets é público (throttle only) para a tela de projeção carregar imagens.
// =====================================================================
$projectionV1 = \Modules\Projection\App\Http\Controllers\Api\V1\ProjectionController::class;
Route::middleware(['throttle:120,1', 'optional_sanctum'])->prefix('v1/projection')->name('projection.api.')->group(function () use ($projectionV1) {
    Route::get('assets/serve/{filename}', [$projectionV1, 'serveAsset'])->name('assets.serve')->where('filename', '[a-zA-Z0-9._-]+');
    Route::get('viewer/state', [$projectionV1, 'getStateForViewer'])->name('state.viewer')->middleware('throttle:120,1');
    Route::get('sync/bundle', [$projectionV1, 'getSyncBundle'])->name('sync.bundle')->middleware('throttle:60,1');
});
// Throttle mais alto: telas fazem polling (1s) + console; 180/min ≈ 3 req/s para várias telas
Route::middleware(['throttle:180,1', 'web', 'auth'])->prefix('v1/projection')->name('projection.api.')->group(function () use ($projectionV1) {
    Route::get('state', [$projectionV1, 'getState'])->name('state');
    Route::post('state', [$projectionV1, 'updateState'])->name('state.update');
    Route::get('assets', [$projectionV1, 'getAssets'])->name('assets.index');
    Route::post('assets', [$projectionV1, 'uploadAsset'])->name('assets.store');
    Route::delete('assets/{id}', [$projectionV1, 'deleteAsset'])->name('assets.destroy');
    Route::get('slides', [$projectionV1, 'getSlides'])->name('slides.index');
    Route::post('slides', [$projectionV1, 'storeSlide'])->name('slides.store');
    Route::put('slides/{id}', [$projectionV1, 'updateSlide'])->name('slides.update');
    Route::delete('slides/{id}', [$projectionV1, 'deleteSlide'])->name('slides.destroy');
    Route::get('lyrics/search', [$projectionV1, 'searchLyrics'])->name('lyrics.search');
    Route::post('timeline/items', [$projectionV1, 'addTimelineItem'])->name('timeline.items.store');
    Route::delete('timeline/items/{id}', [$projectionV1, 'deleteTimelineItem'])->name('timeline.items.destroy');
    Route::post('timeline/reorder', [$projectionV1, 'reorderTimeline'])->name('timeline.reorder');
    Route::get('events/upcoming', [$projectionV1, 'getUpcomingEvents'])->name('events.upcoming');
    Route::get('themes', [$projectionV1, 'getThemes'])->name('themes.index');
    Route::get('themes/{id}', [$projectionV1, 'getTheme'])->name('themes.show');
    Route::post('themes', [$projectionV1, 'storeTheme'])->name('themes.store');
    Route::put('themes/{id}', [$projectionV1, 'updateTheme'])->name('themes.update');
    Route::delete('themes/{id}', [$projectionV1, 'deleteTheme'])->name('themes.destroy');
    Route::post('themes/{id}/default', [$projectionV1, 'setThemeDefault'])->name('themes.default');
    Route::post('state/next-slide', [$projectionV1, 'nextSlide'])->name('state.nextSlide');
    Route::post('state/prev-slide', [$projectionV1, 'prevSlide'])->name('state.prevSlide');
    Route::get('card-templates', [$projectionV1, 'getCardTemplates'])->name('card-templates.index');
    Route::get('card-templates/{id}', [$projectionV1, 'getCardTemplate'])->name('card-templates.show');
});

// =====================================================================
// Treasury API v1 – dashboard, entradas, campanhas, metas, relatórios (formato { data })
// =====================================================================
$treasuryV1 = \Modules\Treasury\App\Http\Controllers\Api\V1\TreasuryController::class;
Route::middleware(['throttle:60,1', 'web', 'auth'])->prefix('v1/treasury')->name('treasury.api.')->group(function () use ($treasuryV1) {
    Route::get('dashboard', [$treasuryV1, 'dashboard'])->name('dashboard');
    Route::get('entry-form-options', [$treasuryV1, 'entryFormOptions'])->name('entry-form-options');
    Route::get('entries', [$treasuryV1, 'entries'])->name('entries.index');
    Route::post('entries', [$treasuryV1, 'storeEntry'])->name('entries.store');
    Route::get('entries/{id}', [$treasuryV1, 'entry'])->name('entries.show');
    Route::put('entries/{id}', [$treasuryV1, 'updateEntry'])->name('entries.update');
    Route::delete('entries/{id}', [$treasuryV1, 'destroyEntry'])->name('entries.destroy');
    Route::post('entries/import-payment/{paymentId}', [$treasuryV1, 'importPayment'])->name('entries.import-payment');
    Route::get('campaigns', [$treasuryV1, 'campaigns'])->name('campaigns.index');
    Route::post('campaigns', [$treasuryV1, 'storeCampaign'])->name('campaigns.store');
    Route::get('campaigns/{id}', [$treasuryV1, 'campaign'])->name('campaigns.show');
    Route::put('campaigns/{id}', [$treasuryV1, 'updateCampaign'])->name('campaigns.update');
    Route::delete('campaigns/{id}', [$treasuryV1, 'destroyCampaign'])->name('campaigns.destroy');
    Route::get('goals', [$treasuryV1, 'goals'])->name('goals.index');
    Route::post('goals', [$treasuryV1, 'storeGoal'])->name('goals.store');
    Route::get('goals/{id}', [$treasuryV1, 'goal'])->name('goals.show');
    Route::put('goals/{id}', [$treasuryV1, 'updateGoal'])->name('goals.update');
    Route::delete('goals/{id}', [$treasuryV1, 'destroyGoal'])->name('goals.destroy');
    Route::get('reports', [$treasuryV1, 'reports'])->name('reports.index');
    Route::get('permissions', [$treasuryV1, 'permissions'])->name('permissions.index');
    Route::get('closings', [$treasuryV1, 'closings'])->name('closings.index');
    Route::post('closings/{id}/approve-for-assembly', [$treasuryV1, 'approveClosingForAssembly'])->name('closings.approve-for-assembly');
});

// =====================================================================
// Assets API v1 – patrimônio (formato { data })
// =====================================================================
$assetsV1 = \Modules\Assets\App\Http\Controllers\Api\V1\AssetController::class;
Route::middleware(['throttle:60,1', 'web', 'auth'])->prefix('v1/assets')->name('assets.api.')->group(function () use ($assetsV1) {
    Route::get('/', [$assetsV1, 'index'])->name('index');
    Route::get('/code/{code}', [$assetsV1, 'getByCode'])->name('by-code');
    Route::get('/{id}', [$assetsV1, 'show'])->name('show');
});

// =====================================================================
// EBD API v1 – central (formato { data })
// =====================================================================
$ebdV1 = \Modules\EBD\App\Http\Controllers\Api\V1\EbdController::class;
Route::middleware(['throttle:60,1', 'web', 'auth', 'verified'])->prefix('v1/ebd')->name('ebd.api.')->group(function () use ($ebdV1) {
    Route::get('dashboard', [$ebdV1, 'dashboard'])->name('dashboard');
    Route::get('classes', [$ebdV1, 'classes'])->name('classes');
    Route::get('classes/{classId}', [$ebdV1, 'classDetail'])->name('classes.show');
    Route::get('lessons', [$ebdV1, 'lessons'])->name('lessons');
    Route::get('lessons/{lessonId}', [$ebdV1, 'lessonDetail'])->name('lessons.show');
    Route::post('lessons/{lessonId}/complete', [$ebdV1, 'markLessonComplete'])->name('lessons.complete');
    Route::get('student/dashboard', [$ebdV1, 'studentDashboard'])->name('student.dashboard');
    Route::get('teacher/dashboard', [$ebdV1, 'teacherDashboard'])->name('teacher.dashboard');
    Route::get('leaderboard', [$ebdV1, 'leaderboard'])->name('leaderboard');
    Route::get('quiz/status/{session}', [$ebdV1, 'quizStatus'])->name('quiz.status');
    Route::post('quiz/submit/{session}', [$ebdV1, 'quizSubmit'])->name('quiz.submit');
});

// =====================================================================
// Ministries API v1 – ministérios (formato { data })
// =====================================================================
$ministriesV1 = \Modules\Ministries\App\Http\Controllers\Api\V1\MinistryController::class;
Route::middleware(['throttle:60,1', 'web', 'auth'])->prefix('v1/ministries')->name('ministries.api.')->group(function () use ($ministriesV1) {
    Route::get('/', [$ministriesV1, 'index'])->name('index');
    Route::get('/{id}', [$ministriesV1, 'show'])->name('show');
    Route::post('/', [$ministriesV1, 'store'])->name('store');
    Route::put('/{id}', [$ministriesV1, 'update'])->name('update');
    Route::delete('/{id}', [$ministriesV1, 'destroy'])->name('destroy');
});

// =====================================================================
// ChurchCouncil API v1 – reuniões do conselho (formato { data })
// =====================================================================
$churchCouncilV1 = \Modules\ChurchCouncil\App\Http\Controllers\Api\V1\ChurchCouncilController::class;
Route::middleware(['throttle:60,1', 'web', 'auth'])->prefix('v1/church-council')->name('churchcouncil.api.')->group(function () use ($churchCouncilV1) {
    Route::get('/', [$churchCouncilV1, 'index'])->name('index');
    Route::get('/members', [$churchCouncilV1, 'members'])->name('members');
    Route::get('/agendas', [$churchCouncilV1, 'agendas'])->name('agendas');
    Route::get('/agendas/{id}', [$churchCouncilV1, 'agendaShow'])->name('agendas.show');
    Route::post('/agendas/{id}/vote', [$churchCouncilV1, 'agendaVote'])->name('agendas.vote');
    Route::get('/approvals', [$churchCouncilV1, 'approvals'])->name('approvals');
    Route::get('/documents', [$churchCouncilV1, 'documents'])->name('documents');
    Route::get('/projects', [$churchCouncilV1, 'projects'])->name('projects');
    Route::get('/{id}', [$churchCouncilV1, 'show'])->name('show');
    Route::post('/', [$churchCouncilV1, 'store'])->name('store');
    Route::put('/{id}', [$churchCouncilV1, 'update'])->name('update');
    Route::delete('/{id}', [$churchCouncilV1, 'destroy'])->name('destroy');
});

// =====================================================================
// SocialAction API v1 – campanhas (formato { data })
// =====================================================================
$socialActionV1 = \Modules\SocialAction\App\Http\Controllers\Api\V1\SocialActionController::class;
Route::middleware(['throttle:60,1', 'web', 'auth'])->prefix('v1/social-actions')->name('socialaction.api.')->group(function () use ($socialActionV1) {
    Route::get('/', [$socialActionV1, 'index'])->name('index');
    Route::get('/{id}', [$socialActionV1, 'show'])->name('show');
    Route::post('/', [$socialActionV1, 'store'])->name('store');
    Route::put('/{id}', [$socialActionV1, 'update'])->name('update');
    Route::delete('/{id}', [$socialActionV1, 'destroy'])->name('destroy');
});

// =====================================================================
// Sermons API v1 – listagem/show pública; store/update/destroy autenticado (formato { data })
// =====================================================================
$sermonsV1 = \Modules\Sermons\App\Http\Controllers\Api\V1\SermonController::class;
$sermonStudyNotesV1 = \Modules\Sermons\App\Http\Controllers\Api\V1\SermonStudyNoteController::class;
Route::middleware(['throttle:60,1'])->prefix('v1/sermons')->name('sermons.api.')->group(function () use ($sermonsV1) {
    Route::get('/', [$sermonsV1, 'index'])->name('index');
    Route::get('/slug/{slug}', [$sermonsV1, 'showBySlug'])->name('show-by-slug');
    Route::get('/{id}', [$sermonsV1, 'show'])->name('show');
});
Route::middleware(['throttle:60,1', 'web', 'auth'])->prefix('v1/sermons')->name('sermons.api.')->group(function () use ($sermonsV1, $sermonStudyNotesV1) {
    Route::post('/', [$sermonsV1, 'store'])->name('store');
    Route::put('/{id}', [$sermonsV1, 'update'])->name('update');
    Route::delete('/{id}', [$sermonsV1, 'destroy'])->name('destroy');
    Route::get('study-notes', [$sermonStudyNotesV1, 'index'])->name('study-notes.index');
    Route::post('study-notes', [$sermonStudyNotesV1, 'store'])->name('study-notes.store');
    Route::get('study-notes/{id}', [$sermonStudyNotesV1, 'show'])->name('study-notes.show');
    Route::put('study-notes/{id}', [$sermonStudyNotesV1, 'update'])->name('study-notes.update');
    Route::delete('study-notes/{id}', [$sermonStudyNotesV1, 'destroy'])->name('study-notes.destroy');
});

// =====================================================================
// API v1 – Autenticação Sanctum
// =====================================================================
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('admins', \Modules\Admin\App\Http\Controllers\AdminController::class)->names('admin');
    Route::apiResource('events', \Modules\Events\App\Http\Controllers\EventsController::class)->names('events');
    Route::apiResource('memberpanels', \Modules\MemberPanel\App\Http\Controllers\MemberPanelController::class)->names('memberpanel');
    Route::apiResource('homepages', \Modules\HomePage\App\Http\Controllers\HomePageController::class)->names('api.homepage');
});

// Bible v1 (auth:sanctum) – apiResource bibles; compare está em api/v1/bible/compare
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('bibles', \Modules\Bible\App\Http\Controllers\BibleController::class)->names('bible');
});

// =====================================================================
// Marketplace API v1 – frete (público para checkout, throttle)
// =====================================================================
Route::middleware(['throttle:60,1'])->prefix('v1/marketplace')->name('marketplace.api.')->group(function () {
    Route::post('freight', [\Modules\Marketplace\Http\Controllers\Api\FreightController::class, 'calculate'])->name('freight');
});

// =====================================================================
// Intercessor: não possui API própria. Usar api/v1/bible/search diretamente (sem rota legada).
// =====================================================================
