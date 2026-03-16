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

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    
    // Route untuk autorisasi user role developer
    Route::middleware(['auth', 'role:developer'])->group(function () {
        Route::get('/developer/dashboard', [DashboardController::class, 'index'])->name('developer.dashboard');
        // Manajemen User
        Route::post('developer/users/bulk-update-wilayah', [UserController::class, 'bulkUpdateWilayah'])->name('developer.users.bulk-update-wilayah');
        Route::post('developer/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('developer.users.bulk-delete');
        Route::resource('developer/users', UserController::class)->names('developer.users');
        Route::post('developer/users/import', [UserController::class, 'import'])->name('developer.users.import');
        Route::get('developer/users/template/download', [UserController::class, 'downloadTemplate'])->name('developer.users.template');
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
        Route::get('/pc/dashboard', [DashboardController::class, 'index'])->name('pc.dashboard');
    });

    // Route untuk profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
