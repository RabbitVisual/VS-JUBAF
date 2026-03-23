<?php

use Illuminate\Support\Facades\Route;
use Modules\Treasury\App\Http\Middleware\CheckTreasuryPermission;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for this module.
|
*/

Route::middleware(['auth', 'verified'])->prefix('treasury')->name('treasury.')->group(function () {
    // Dashboard
    Route::get('/', [\Modules\Treasury\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [\Modules\Treasury\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard.index');

    // Financial Entries
    Route::middleware([CheckTreasuryPermission::class.':view_reports'])->group(function () {
        Route::get('/entries', [\Modules\Treasury\App\Http\Controllers\Admin\FinancialEntryController::class, 'index'])->name('entries.index');
        Route::get('/entries/import/{payment}', [\Modules\Treasury\App\Http\Controllers\Admin\FinancialEntryController::class, 'importPayment'])->name('entries.import');
    });

    Route::middleware([CheckTreasuryPermission::class.':create_entries'])->group(function () {
        Route::get('/entries/create', [\Modules\Treasury\App\Http\Controllers\Admin\FinancialEntryController::class, 'create'])->name('entries.create');
        Route::post('/entries', [\Modules\Treasury\App\Http\Controllers\Admin\FinancialEntryController::class, 'store'])->name('entries.store');
    });

    Route::middleware([CheckTreasuryPermission::class.':edit_entries'])->group(function () {
        Route::get('/entries/{entry}/edit', [\Modules\Treasury\App\Http\Controllers\Admin\FinancialEntryController::class, 'edit'])->name('entries.edit');
        Route::put('/entries/{entry}', [\Modules\Treasury\App\Http\Controllers\Admin\FinancialEntryController::class, 'update'])->name('entries.update');
    });

    Route::middleware([CheckTreasuryPermission::class.':create_entries'])->group(function () {
        Route::post('/entries/{entry}/reverse', [\Modules\Treasury\App\Http\Controllers\Admin\FinancialEntryController::class, 'reverse'])->name('entries.reverse');
    });

    Route::middleware([CheckTreasuryPermission::class.':delete_entries'])->group(function () {
        Route::delete('/entries/{entry}', [\Modules\Treasury\App\Http\Controllers\Admin\FinancialEntryController::class, 'destroy'])->name('entries.destroy');
    });

    // Campaigns
    Route::middleware([CheckTreasuryPermission::class.':manage_campaigns'])->group(function () {
        Route::get('/campaigns/create', [\Modules\Treasury\App\Http\Controllers\Admin\CampaignController::class, 'create'])->name('campaigns.create');
        Route::post('/campaigns', [\Modules\Treasury\App\Http\Controllers\Admin\CampaignController::class, 'store'])->name('campaigns.store');
        Route::get('/campaigns/{campaign}/edit', [\Modules\Treasury\App\Http\Controllers\Admin\CampaignController::class, 'edit'])->name('campaigns.edit');
        Route::put('/campaigns/{campaign}', [\Modules\Treasury\App\Http\Controllers\Admin\CampaignController::class, 'update'])->name('campaigns.update');
        Route::delete('/campaigns/{campaign}', [\Modules\Treasury\App\Http\Controllers\Admin\CampaignController::class, 'destroy'])->name('campaigns.destroy');
    });

    Route::middleware([CheckTreasuryPermission::class.':view_reports'])->group(function () {
        Route::get('/campaigns', [\Modules\Treasury\App\Http\Controllers\Admin\CampaignController::class, 'index'])->name('campaigns.index');
        Route::get('/campaigns/{campaign}', [\Modules\Treasury\App\Http\Controllers\Admin\CampaignController::class, 'show'])->name('campaigns.show');
    });

    // Financial Goals
    Route::middleware([CheckTreasuryPermission::class.':manage_goals'])->group(function () {
        Route::get('/goals/create', [\Modules\Treasury\App\Http\Controllers\Admin\FinancialGoalController::class, 'create'])->name('goals.create');
        Route::post('/goals', [\Modules\Treasury\App\Http\Controllers\Admin\FinancialGoalController::class, 'store'])->name('goals.store');
        Route::get('/goals/{goal}/edit', [\Modules\Treasury\App\Http\Controllers\Admin\FinancialGoalController::class, 'edit'])->name('goals.edit');
        Route::put('/goals/{goal}', [\Modules\Treasury\App\Http\Controllers\Admin\FinancialGoalController::class, 'update'])->name('goals.update');
        Route::delete('/goals/{goal}', [\Modules\Treasury\App\Http\Controllers\Admin\FinancialGoalController::class, 'destroy'])->name('goals.destroy');
    });

    Route::middleware([CheckTreasuryPermission::class.':view_reports'])->group(function () {
        Route::get('/goals', [\Modules\Treasury\App\Http\Controllers\Admin\FinancialGoalController::class, 'index'])->name('goals.index');
        Route::get('/goals/{goal}', [\Modules\Treasury\App\Http\Controllers\Admin\FinancialGoalController::class, 'show'])->name('goals.show');
    });

    // Permissions
    Route::middleware([CheckTreasuryPermission::class.':is_admin'])->group(function () {
        Route::get('/permissions', [\Modules\Treasury\App\Http\Controllers\Admin\TreasuryPermissionController::class, 'index'])->name('permissions.index');
        Route::get('/permissions/create', [\Modules\Treasury\App\Http\Controllers\Admin\TreasuryPermissionController::class, 'create'])->name('permissions.create');
        Route::post('/permissions', [\Modules\Treasury\App\Http\Controllers\Admin\TreasuryPermissionController::class, 'store'])->name('permissions.store');
        Route::get('/permissions/{treasuryPermission}/edit', [\Modules\Treasury\App\Http\Controllers\Admin\TreasuryPermissionController::class, 'edit'])->name('permissions.edit');
        Route::put('/permissions/{treasuryPermission}', [\Modules\Treasury\App\Http\Controllers\Admin\TreasuryPermissionController::class, 'update'])->name('permissions.update');
        Route::delete('/permissions/{treasuryPermission}', [\Modules\Treasury\App\Http\Controllers\Admin\TreasuryPermissionController::class, 'destroy'])->name('permissions.destroy');
    });

    // Reports
    Route::middleware([CheckTreasuryPermission::class.':view_reports'])->group(function () {
        Route::get('/reports', [\Modules\Treasury\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/closings/{closing}/approve-for-assembly', [\Modules\Treasury\App\Http\Controllers\Admin\ReportController::class, 'approveClosingForAssembly'])->name('reports.closing.approve-for-assembly');
        Route::get('/reports/contribution-receipt', [\Modules\Treasury\App\Http\Controllers\Admin\ReportController::class, 'contributionReceiptPdf'])->name('reports.contribution-receipt');
    });

    Route::middleware([CheckTreasuryPermission::class.':export_data'])->group(function () {
        Route::get('/reports/export/excel', [\Modules\Treasury\App\Http\Controllers\Admin\ReportController::class, 'exportExcel'])->name('reports.export.excel');
        Route::get('/reports/export/pdf', [\Modules\Treasury\App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('reports.export.pdf');
        Route::get('/reports/export/tithes-pdf', [\Modules\Treasury\App\Http\Controllers\Admin\ReportController::class, 'exportTithesOfferingsPdf'])->name('reports.export.tithes.pdf');
        Route::get('/reports/export', [\Modules\Treasury\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export'); // Legacy
    });
});
