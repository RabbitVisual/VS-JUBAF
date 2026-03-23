<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Gamification module web routes
|--------------------------------------------------------------------------
| Bot and analytics routes are registered in routes/member.php (member panel).
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Placeholder: bot/dashboard routes live in routes/member.php
});
