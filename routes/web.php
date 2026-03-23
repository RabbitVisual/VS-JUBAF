<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\QuickLoginController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Rotas Públicas e Web (Centralizadas)
|--------------------------------------------------------------------------
|
| Rotas acessíveis sem autenticação (públicas) e rotas de autenticação.
| Não inclui: /admin (routes/admin.php) nem /painel|ebd/member (routes/member.php).
| Middleware 'web' aplicado automaticamente pelo bootstrap.
|
*/

// =====================================================================
// Storage público (fallback quando symlink não funciona, ex.: XAMPP/Windows)
// =====================================================================
Route::get('/storage/{path}', function (string $path) {
    if (str_contains($path, '..')) {
        abort(404);
    }
    $disk = Storage::disk('public');
    if (! $disk->exists($path)) {
        abort(404);
    }

    return $disk->response($path, null, ['Cache-Control' => 'public, max-age=31536000']);
})->where('path', '.+')->name('storage.serve');

// =====================================================================
// Autenticação
// =====================================================================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/login/2fa', [LoginController::class, 'showTwoFactorForm'])->name('login.2fa.form');
Route::post('/login/2fa', [LoginController::class, 'verifyTwoFactor'])->name('login.2fa.verify');

// Acesso administrativo durante manutenção (rota em exceção no maintenance)
Route::get('/admin/acesso-mestre', [\App\Http\Controllers\Auth\AdminAccessController::class, 'showForm'])->name('admin.acesso-mestre');
Route::post('/admin/acesso-mestre', [\App\Http\Controllers\Auth\AdminAccessController::class, 'login'])->name('admin.acesso-mestre.login');

Route::get('/forgot-password', function () {
    return view('homepage::auth.forgot-password');
})->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordRecoveryController::class, 'sendResetLink'])->name('password.email');

Route::get('/reset-password/{token}', function ($token) {
    return view('homepage::auth.reset-password', ['token' => $token]);
})->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])->name('password.update');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

if (app()->environment('local', 'development', 'dev')) {
    Route::post('/quick-login', [QuickLoginController::class, 'quickLogin'])->name('quick-login');
}

// =====================================================================
// HomePage - Página inicial, newsletter, contato, páginas públicas
// =====================================================================
Route::get('/', [\Modules\HomePage\App\Http\Controllers\HomePageController::class, 'index'])->name('homepage.index');

Route::post('/newsletter/subscribe', [\Modules\HomePage\App\Http\Controllers\HomePageController::class, 'subscribeNewsletter'])->name('newsletter.subscribe');
Route::post('/newsletter/unsubscribe', [\Modules\HomePage\App\Http\Controllers\HomePageController::class, 'unsubscribeNewsletter'])->name('newsletter.unsubscribe');
Route::post('/contato/enviar', [\Modules\HomePage\App\Http\Controllers\HomePageController::class, 'sendMessage'])->name('contact.send');

Route::prefix('testemunhos')->name('testimonials.')->group(function () {
    Route::get('/', [\Modules\HomePage\App\Http\Controllers\PublicController::class, 'testimonials'])->name('index');
    Route::get('/{testimonial}', [\Modules\HomePage\App\Http\Controllers\PublicController::class, 'showTestimonial'])->name('show');
});

Route::prefix('galeria')->name('gallery.')->group(function () {
    Route::get('/', [\Modules\HomePage\App\Http\Controllers\PublicController::class, 'gallery'])->name('index');
    Route::get('/{image}', [\Modules\HomePage\App\Http\Controllers\PublicController::class, 'showGalleryImage'])->name('show');
});

Route::get('/ministerios', [\Modules\HomePage\App\Http\Controllers\PublicController::class, 'ministries'])->name('ministries.index');
Route::get('/versiculo/contexto', [\Modules\HomePage\App\Http\Controllers\PublicController::class, 'verseContext'])->name('verse.context');
Route::get('/radio', [\Modules\HomePage\App\Http\Controllers\HomePageController::class, 'radio'])->name('homepage.radio');

Route::post('/auth/check-user', [\App\Http\Controllers\Auth\AuthCheckController::class, 'checkUserExist'])->name('auth.check-user');

// =====================================================================
// Events - Eventos públicos (entrada única: eventos/{slug}, fluxo inscrição unificado)
// =====================================================================
Route::prefix('eventos')->name('events.public.')->group(function () {
    Route::get('/', [\Modules\Events\App\Http\Controllers\Public\EventController::class, 'index'])->name('index');
    Route::get('/{event:slug}', [\Modules\Events\App\Http\Controllers\Public\EventController::class, 'show'])->name('show');
    Route::get('/{event:slug}/landing', [\Modules\Events\App\Http\Controllers\Public\EventController::class, 'landing'])->name('landing');
    Route::get('/{event:slug}/inscrever', function ($event) {
        $slug = is_object($event) ? $event->slug : $event;
        return redirect(route('events.public.landing', $slug) . '?openRegistration=1', 302);
    })->name('inscrever');
    Route::get('/{event:slug}/comprar', function ($event) {
        $slug = is_object($event) ? $event->slug : $event;
        return redirect(route('events.public.landing', $slug) . '?openRegistration=1', 302);
    })->name('comprar');
    Route::post('/{event:slug}/register', [\Modules\Events\App\Http\Controllers\Public\EventController::class, 'register'])->name('register');
    Route::get('/registration/{registration}/pending', function ($registration) {
        $registration = \Modules\Events\App\Models\EventRegistration::findOrFail($registration);
        return view('events::public.registration.pending', compact('registration'));
    })->name('registration.pending');
    Route::get('/registration/{registration}/confirmed', [\Modules\Events\App\Http\Controllers\Public\EventController::class, 'showRegistrationConfirmed'])->name('registration.confirmed');
    Route::get('/inscricao/{uuid}/pagar', [\Modules\Events\App\Http\Controllers\Public\EventController::class, 'showPaymentPage'])->name('payment');
    Route::post('/inscricao/{uuid}/iniciar-pagamento', [\Modules\Events\App\Http\Controllers\Public\EventController::class, 'startPayment'])->name('payment.start');
    Route::get('/inscricao/{uuid}/ingresso', [\Modules\Events\App\Http\Controllers\Public\EventController::class, 'downloadTicket'])->name('ticket.download');
    Route::get('/inscricao/{uuid}/certificado', [\Modules\Events\App\Http\Controllers\Public\EventController::class, 'downloadCertificate'])->name('certificate.download');
});

// Redirect eventos-v2 to eventos (unified flow: landing + modal)
Route::get('/eventos-v2/{slug}/checkout', function (string $slug) {
    return redirect(route('events.public.landing', $slug) . '?openRegistration=1', 302);
})->name('events-v2.public.checkout.show');
Route::get('/eventos-v2/checkout/confirmation/{uuid}', function (string $uuid) {
    return redirect()->route('events.public.payment', ['uuid' => $uuid], 302);
})->name('events-v2.public.checkout.confirmation');
Route::get('/eventos-v2/ticket/{uuid}/download', function (string $uuid) {
    return redirect()->route('events.public.ticket.download', ['uuid' => $uuid], 302);
})->name('events-v2.public.checkout.download');
Route::post('/eventos-v2/{slug}/checkout', function (string $slug) {
    return redirect(route('events.public.landing', $slug) . '?openRegistration=1', 302);
});

// =====================================================================
// PaymentGateway - Doação pública, checkout, webhooks
// =====================================================================
Route::prefix('doacao')->name('donation.')->group(function () {
    Route::get('/', [\Modules\PaymentGateway\App\Http\Controllers\Public\DonationController::class, 'create'])->name('create');
    Route::post('/', [\Modules\PaymentGateway\App\Http\Controllers\Public\DonationController::class, 'store'])->name('store');
    Route::get('/{transactionId}', [\Modules\PaymentGateway\App\Http\Controllers\Public\DonationController::class, 'show'])->name('show');
});

Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/qr', [\Modules\PaymentGateway\App\Http\Controllers\Public\QrCodeController::class, 'show'])->name('qr');
    Route::get('/{transactionId}', [\Modules\PaymentGateway\App\Http\Controllers\Public\CheckoutController::class, 'show'])->name('show');
});

// =====================================================================
// Bible - Bíblia pública (sem login, totalmente responsiva)
// =====================================================================
Route::prefix('biblia-online')->name('bible.public.')->group(function () {
    Route::get('/', [\Modules\Bible\App\Http\Controllers\PublicBibleController::class, 'index'])->name('index');
    Route::get('/buscar', [\Modules\Bible\App\Http\Controllers\PublicBibleController::class, 'search'])->name('search');
    Route::get('/versao/{versionAbbr?}', [\Modules\Bible\App\Http\Controllers\PublicBibleController::class, 'read'])->name('read')->where('versionAbbr', '[A-Za-z0-9_-]+');
    Route::get('/versao/{versionAbbr}/livro/{bookNumber}', [\Modules\Bible\App\Http\Controllers\PublicBibleController::class, 'book'])->name('book')->where(['versionAbbr' => '[A-Za-z0-9_-]+', 'bookNumber' => '[0-9]+']);
    Route::get('/versao/{versionAbbr}/livro/{bookNumber}/capitulo/{chapterNumber}', [\Modules\Bible\App\Http\Controllers\PublicBibleController::class, 'chapter'])->name('chapter')->where(['versionAbbr' => '[A-Za-z0-9_-]+', 'bookNumber' => '[0-9]+', 'chapterNumber' => '[0-9]+']);
});

// =====================================================================
// ChurchCouncil - Rotas públicas (placeholder para futuro)
// =====================================================================
Route::prefix('conselho')->name('public.churchcouncil.')->group(function () {
    // Informações públicas do conselho, se necessário
});
