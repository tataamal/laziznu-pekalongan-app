<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Developer\DashboardController;
use App\Http\Controllers\Developer\UserController;
use App\Http\Controllers\Developer\WilayahController;
use App\Http\Controllers\Ranting\InputPemasukanController;
use App\Http\Controllers\Ranting\RantingDashboardController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

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

    Route::middleware(['auth', 'role:ranting'])->group(function () {
        Route::get('/ranting/income', [InputPemasukanController::class, 'index'])->name('ranting.income.index');
        Route::get('/ranting/dashboard', [RantingDashboardController::class, 'index'])->name('ranting.dashboard');
        Route::delete('/ranting/income/bulk-delete', [InputPemasukanController::class, 'bulkDelete'])->name('ranting.income.bulk-delete');
        Route::resource('ranting/income', InputPemasukanController::class)->names('ranting.income');
        
        // Pentasarufan (Distribution)
        Route::delete('/ranting/distribution/bulk-delete', [\App\Http\Controllers\Ranting\DistributionController::class, 'bulkDelete'])->name('ranting.distribution.bulk-delete');
        Route::resource('ranting/distribution', \App\Http\Controllers\Ranting\DistributionController::class)->names('ranting.distribution');

        Route::view('/ranting/call-center', 'ranting.call-center')->name('ranting.call-center');
    });

    Route::get('/pc/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:pc,developer')
        ->name('pc.dashboard');

    Route::get('/mwc/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:mwc,developer')
        ->name('mwc.dashboard');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
