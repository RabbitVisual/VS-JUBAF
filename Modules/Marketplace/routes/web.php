<?php

use Illuminate\Support\Facades\Route;
use Modules\Marketplace\Http\Controllers\MarketplaceController;
use Modules\Marketplace\Http\Controllers\Public\CheckoutController;
use Modules\Marketplace\Http\Controllers\Public\MarketplaceCustomerAuthController;
use Modules\Marketplace\Http\Controllers\Public\StorefrontController;
use Modules\Marketplace\Http\Middleware\SyncMarketplaceCartFromDb;

Route::middleware([SyncMarketplaceCartFromDb::class])->prefix('loja')->name('marketplace.storefront.')->group(function () {
    Route::get('/', [StorefrontController::class, 'index'])->name('index');
    Route::get('/produto/{slugOrUuid}', [StorefrontController::class, 'show'])->name('show');
    Route::get('/politica/{slug}', [StorefrontController::class, 'policyPage'])->name('policy')->where('slug', 'politica-entrega|trocas|termos');
    Route::get('/carrinho', [CheckoutController::class, 'cart'])->name('cart');
    Route::get('/carrinho/remover', [CheckoutController::class, 'removeCartItem'])->name('cart.remove');
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('coupon.apply');
    Route::post('/checkout/coupon/remove', [CheckoutController::class, 'removeCoupon'])->name('coupon.remove');
    Route::get('/obrigado', [StorefrontController::class, 'thankYou'])->name('thank-you');
});

Route::prefix('loja/cliente')->name('marketplace.customer.')->group(function () {
    Route::middleware('guest:marketplace')->group(function () {
        Route::get('/registrar', [MarketplaceCustomerAuthController::class, 'showRegister'])->name('register');
        Route::post('/registrar', [MarketplaceCustomerAuthController::class, 'register'])->name('register.post');
        Route::get('/login', [MarketplaceCustomerAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [MarketplaceCustomerAuthController::class, 'login'])->name('login.post');
    });

    Route::middleware('auth:marketplace')->group(function () {
        Route::post('/logout', [MarketplaceCustomerAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [MarketplaceCustomerAuthController::class, 'dashboard'])->name('dashboard');
        Route::get('/perfil', [MarketplaceCustomerAuthController::class, 'profile'])->name('profile');
        Route::put('/perfil', [MarketplaceCustomerAuthController::class, 'updateProfile'])->name('profile.update');
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('marketplaces', MarketplaceController::class)->names('marketplace');
});
