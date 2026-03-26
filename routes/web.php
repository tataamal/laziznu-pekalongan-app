<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Developer\DashboardController;
use App\Http\Controllers\Developer\UserController;
use App\Http\Controllers\Developer\WilayahController;
use App\Http\Controllers\Developer\ManagementRantingController;
use App\Http\Controllers\Developer\ManagementMunfiqController;
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
use App\Http\Controllers\Pc\ReportController;
use App\Http\Controllers\Mwc\ReportController as MwcReportController;
use App\Http\Controllers\Ranting\ReportController as RantingReportController;
use App\Http\Controllers\Ranting\ManagementMunfiqController as RantingMunfiqController;
use App\Http\Controllers\Mwc\ManagementMunfiqController as MwcMunfiqController;
use App\Http\Controllers\Pc\ManagementMunfiqController as PcMunfiqController;


Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/api/stats/income', [LandingController::class, 'getIncomeByRanting']);
Route::get('/api/stats/distribution', [LandingController::class, 'getDistributionByRanting']);
Route::get('/api/stats/infaq', [LandingController::class, 'getInfaqStats']);

Route::middleware(['auth'])->group(function () {
    Route::middleware(['auth', 'role:developer'])->group(function () {
        Route::get('/developer/dashboard', [DashboardController::class, 'index'])->name('developer.dashboard');
        Route::post('developer/users/bulk-update-wilayah', [UserController::class, 'bulkUpdateWilayah'])->name('developer.users.bulk-update-wilayah');
        Route::post('developer/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('developer.users.bulk-delete');
        Route::resource('developer/users', UserController::class)->names('developer.users');
        Route::post('developer/users/import', [UserController::class, 'import'])->name('developer.users.import');
        Route::get('developer/users/template/download', [UserController::class, 'downloadTemplate'])->name('developer.users.template');
        Route::post('developer/wilayah/bulk-delete', [WilayahController::class, 'bulkDelete'])->name('developer.wilayah.bulk-delete');
        Route::post('developer/wilayah/import', [WilayahController::class, 'import'])->name('developer.wilayah.import');
        Route::get('developer/wilayah/template/download', [WilayahController::class, 'downloadTemplate'])->name('developer.wilayah.template');
        Route::resource('developer/wilayah', WilayahController::class)->names('developer.wilayah');
        Route::post('developer/management-ranting/import', [ManagementRantingController::class, 'import'])->name('developer.management-ranting.import');
        Route::get('developer/management-ranting/template', [ManagementRantingController::class, 'template'])->name('developer.management-ranting.template');
        Route::delete('developer/management-ranting/bulk-delete', [ManagementRantingController::class, 'bulkDelete'])->name('developer.management-ranting.bulk-delete');
        Route::resource('developer/management-ranting', ManagementRantingController::class)->names('developer.management-ranting');
        Route::post('developer/management-munfiq/import', [ManagementMunfiqController::class, 'import'])->name('developer.management-munfiq.import');
        Route::get('developer/management-munfiq/template', [ManagementMunfiqController::class, 'downloadTemplate'])->name('developer.management-munfiq.template');
        Route::delete('developer/management-munfiq/bulk-delete', [ManagementMunfiqController::class, 'bulkDelete'])->name('developer.management-munfiq.bulk-delete');
        Route::resource('developer/management-munfiq', ManagementMunfiqController::class)->names('developer.management-munfiq');
    });

    Route::middleware(['auth', 'role:ranting'])->group(function () {
        Route::get('/ranting/income', [InputPemasukanController::class, 'index'])->name('ranting.income.index');
        Route::get('/ranting/dashboard', [RantingDashboardController::class, 'index'])->name('ranting.dashboard');
        Route::delete('/ranting/income/bulk-delete', [InputPemasukanController::class, 'bulkDelete'])->name('ranting.income.bulk-delete');
        Route::resource('ranting/income', InputPemasukanController::class)->names('ranting.income');
        Route::delete('/ranting/distribution/bulk-delete', [DistributionController::class, 'bulkDelete'])->name('ranting.distribution.bulk-delete');
        Route::resource('ranting/distribution', DistributionController::class)->names('ranting.distribution');
        Route::get('/ranting/export-report', [RantingReportController::class, 'index'])->name('ranting.export-report.index');
        Route::post('/ranting/export-report/export', [RantingReportController::class, 'export'])->name('ranting.export-report.export');
        Route::view('/ranting/call-center', 'ranting.call-center')->name('ranting.call-center');
        Route::delete('/ranting/management-munfiq/bulk-delete', [RantingMunfiqController::class, 'bulkDelete'])->name('ranting.management-munfiq.bulk-delete');
        Route::resource('ranting/management-munfiq', RantingMunfiqController::class)->names('ranting.management-munfiq');
    });

    Route::middleware(['auth', 'role:mwc'])->group(function () {
        Route::get('/mwc/dashboard', [MwcDashboardController::class, 'index'])->name('mwc.dashboard');
        Route::get('/mwc/approval-income-koin-nu', [ApprovalIncomeKoinNU::class, 'index'])->name('mwc.approval-income-koin-nu');
        Route::post('/mwc/approval-income-koin-nu/{id}/approve', [ApprovalIncomeKoinNU::class, 'approve'])->name('mwc.approval-income-koin-nu.approve');
        Route::post('/mwc/approval-income-koin-nu/{id}/reject', [ApprovalIncomeKoinNU::class, 'reject'])->name('mwc.approval-income-koin-nu.reject');
        Route::get('/mwc/approval-distribution-koin-nu', [ApprovalDistributionKoinNU::class, 'index'])->name('mwc.approval-distribution-koin-nu');
        Route::post('/mwc/approval-distribution-koin-nu/{id}/approve', [ApprovalDistributionKoinNU::class, 'approve'])->name('mwc.approval-distribution-koin-nu.approve');
        Route::post('/mwc/approval-distribution-koin-nu/{id}/reject', [ApprovalDistributionKoinNU::class, 'reject'])->name('mwc.approval-distribution-koin-nu.reject');
        Route::delete('/mwc/infaq-transaction/bulk-delete', [InfaqTransactionController::class, 'bulkDelete'])->name('mwc.infaq-transaction.bulk-delete');
        Route::resource('mwc/infaq-transaction', InfaqTransactionController::class)->names('mwc.infaq-transaction');
        Route::view('/mwc/call-center', 'mwc.call-center')->name('mwc.call-center');
        Route::get('/mwc/export-report', [MwcReportController::class, 'index'])->name('mwc.export-report.index');
        Route::post('/mwc/export-report/export', [MwcReportController::class, 'export'])->name('mwc.export-report.export');
        Route::delete('/mwc/management-munfiq/bulk-delete', [MwcMunfiqController::class, 'bulkDelete'])->name('mwc.management-munfiq.bulk-delete');
        Route::resource('mwc/management-munfiq', MwcMunfiqController::class)->names('mwc.management-munfiq');
    });

    Route::middleware(['auth', 'role:pc'])->group(function () {
        Route::get('/pc/dashboard', [PcDashboardController::class, 'index'])->name('pc.dashboard');
        Route::delete('/pc/infaq/bulk-delete', [PcInfaqController::class, 'bulkDelete'])->name('pc.infaq.bulk-delete');
        Route::resource('pc/infaq', PcInfaqController::class)->names('pc.infaq');
        Route::get('/pc/data-transaksi-mwc', [DataTransaksiMWC::class, 'index'])->name('pc.data-transaksi-mwc');
        Route::get('/pc/data-transaksi-ranting', [DataTransaksiRanting::class, 'index'])->name('pc.data-transaksi-ranting');
        Route::view('/pc/call-center', 'pc.call-center')->name('pc.call-center');
        Route::get('/pc/export-report', [ReportController::class, 'index'])->name('pc.export-report.index');
        Route::post('/pc/export-report/export', [ReportController::class, 'export'])->name('pc.export-report.export');
        Route::delete('/pc/management-munfiq/bulk-delete', [PcMunfiqController::class, 'bulkDelete'])->name('pc.management-munfiq.bulk-delete');
        Route::resource('pc/management-munfiq', PcMunfiqController::class)->names('pc.management-munfiq');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
