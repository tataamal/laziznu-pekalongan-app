<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Developer\DashboardController;
use App\Http\Controllers\Developer\UserController;
use App\Http\Controllers\Developer\WilayahController;
use App\Http\Controllers\Ranting\InputPemasukanController;
use App\Http\Controllers\Ranting\RantingDashboardController;
use App\Http\Controllers\Ranting\DistributionController;
use App\Http\Controllers\Mwc\MwcDashboardController;
use App\Http\Controllers\Mwc\ApprovalIncomeKoinNU;
use App\Http\Controllers\Mwc\ApprovalDistributionKoinNU;
use App\Http\Controllers\Mwc\InfaqTransactionController;
use App\Http\Controllers\Pc\PcDashboardController;
use App\Http\Controllers\Pc\InfaqController as PcInfaqController;
use App\Http\Controllers\Pc\DataTransaksiMWC;
use App\Http\Controllers\Pc\DataTransaksiRanting;
use App\Http\Controllers\LandingController;


Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/api/stats/income', [LandingController::class, 'getIncomeByRanting']);
Route::get('/api/stats/distribution', [LandingController::class, 'getDistributionByRanting']);
Route::get('/api/stats/infaq', [LandingController::class, 'getInfaqStats']);

Route::middleware(['auth'])->group(function () {
    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // })->name('dashboard');

    
    // Route untuk autorisasi user role developer
    Route::middleware(['auth', 'role:developer'])->group(function () {
        Route::get('/developer/dashboard', [DashboardController::class, 'index'])->name('developer.dashboard');

        // Manajemen User
        Route::post('developer/users/bulk-update-wilayah', [\App\Http\Controllers\Developer\UserController::class, 'bulkUpdateWilayah'])->name('developer.users.bulk-update-wilayah');
        Route::post('developer/users/bulk-delete', [\App\Http\Controllers\Developer\UserController::class, 'bulkDelete'])->name('developer.users.bulk-delete');
        Route::resource('developer/users', \App\Http\Controllers\Developer\UserController::class)->names('developer.users');
        Route::post('developer/users/import', [\App\Http\Controllers\Developer\UserController::class, 'import'])->name('developer.users.import');
        Route::get('developer/users/template/download', [\App\Http\Controllers\Developer\UserController::class, 'downloadTemplate'])->name('developer.users.template');

        // Manajemen Wilayah
        Route::post('developer/wilayah/bulk-delete', [WilayahController::class, 'bulkDelete'])->name('developer.wilayah.bulk-delete');
        Route::post('developer/wilayah/import', [WilayahController::class, 'import'])->name('developer.wilayah.import');
        Route::get('developer/wilayah/template/download', [WilayahController::class, 'downloadTemplate'])->name('developer.wilayah.template');
        Route::resource('developer/wilayah', WilayahController::class)->names('developer.wilayah');
    });

    // Route untuk autorisasi user role ranting
    Route::middleware(['auth', 'role:ranting'])->group(function () {
        Route::get('/ranting/income', [InputPemasukanController::class, 'index'])->name('ranting.income.index');
        Route::get('/ranting/dashboard', [RantingDashboardController::class, 'index'])->name('ranting.dashboard');
        Route::delete('/ranting/income/bulk-delete', [InputPemasukanController::class, 'bulkDelete'])->name('ranting.income.bulk-delete');
        Route::resource('ranting/income', InputPemasukanController::class)->names('ranting.income');
        Route::delete('/ranting/distribution/bulk-delete', [DistributionController::class, 'bulkDelete'])->name('ranting.distribution.bulk-delete');
        Route::resource('ranting/distribution', DistributionController::class)->names('ranting.distribution');
        Route::view('/ranting/call-center', 'ranting.call-center')->name('ranting.call-center');
    });

    Route::middleware(['auth', 'role:mwc'])->group(function () {
        Route::get('/mwc/dashboard', [MwcDashboardController::class, 'index'])->name('mwc.dashboard');
        
        // Income Approval
        Route::get('/mwc/approval-income-koin-nu', [ApprovalIncomeKoinNU::class, 'index'])->name('mwc.approval-income-koin-nu');
        Route::post('/mwc/approval-income-koin-nu/{id}/approve', [ApprovalIncomeKoinNU::class, 'approve'])->name('mwc.approval-income-koin-nu.approve');
        Route::post('/mwc/approval-income-koin-nu/{id}/reject', [ApprovalIncomeKoinNU::class, 'reject'])->name('mwc.approval-income-koin-nu.reject');

        // Distribution Approval
        Route::get('/mwc/approval-distribution-koin-nu', [ApprovalDistributionKoinNU::class, 'index'])->name('mwc.approval-distribution-koin-nu');
        Route::post('/mwc/approval-distribution-koin-nu/{id}/approve', [ApprovalDistributionKoinNU::class, 'approve'])->name('mwc.approval-distribution-koin-nu.approve');
        Route::post('/mwc/approval-distribution-koin-nu/{id}/reject', [ApprovalDistributionKoinNU::class, 'reject'])->name('mwc.approval-distribution-koin-nu.reject');

        // Infaq Transaction
        Route::delete('/mwc/infaq-transaction/bulk-delete', [InfaqTransactionController::class, 'bulkDelete'])->name('mwc.infaq-transaction.bulk-delete');
        Route::resource('mwc/infaq-transaction', InfaqTransactionController::class)->names('mwc.infaq-transaction');
        
        Route::view('/mwc/call-center', 'mwc.call-center')->name('mwc.call-center');
    });

    // Route untuk autorisasi user role pc
    Route::middleware(['auth', 'role:pc'])->group(function () {
        Route::get('/pc/dashboard', [PcDashboardController::class, 'index'])->name('pc.dashboard');

        // PC Infaq Transaction
        Route::delete('/pc/infaq/bulk-delete', [PcInfaqController::class, 'bulkDelete'])->name('pc.infaq.bulk-delete');
        Route::resource('pc/infaq', PcInfaqController::class)->names('pc.infaq');

        // Data Transaksi
        Route::get('/pc/data-transaksi-mwc', [DataTransaksiMWC::class, 'index'])->name('pc.data-transaksi-mwc');
        Route::get('/pc/data-transaksi-ranting', [DataTransaksiRanting::class, 'index'])->name('pc.data-transaksi-ranting');

        // Call Center
        Route::view('/pc/call-center', 'pc.call-center')->name('pc.call-center');
    });

    // Route untuk profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
