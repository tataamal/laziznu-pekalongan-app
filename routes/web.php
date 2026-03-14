<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Developer\DashboardController;

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
        Route::post('developer/users/bulk-update-wilayah', [\App\Http\Controllers\Developer\UserController::class, 'bulkUpdateWilayah'])->name('developer.users.bulk-update-wilayah');
        Route::post('developer/users/bulk-delete', [\App\Http\Controllers\Developer\UserController::class, 'bulkDelete'])->name('developer.users.bulk-delete');
        Route::resource('developer/users', \App\Http\Controllers\Developer\UserController::class)->names('developer.users');
        Route::post('developer/users/import', [\App\Http\Controllers\Developer\UserController::class, 'import'])->name('developer.users.import');
        Route::get('developer/users/template/download', [\App\Http\Controllers\Developer\UserController::class, 'downloadTemplate'])->name('developer.users.template');
        
        // Manajemen Wilayah
        Route::post('developer/wilayah/bulk-delete', [\App\Http\Controllers\Developer\WilayahController::class, 'bulkDelete'])->name('developer.wilayah.bulk-delete');
        Route::post('developer/wilayah/import', [\App\Http\Controllers\Developer\WilayahController::class, 'import'])->name('developer.wilayah.import');
        Route::get('developer/wilayah/template/download', [\App\Http\Controllers\Developer\WilayahController::class, 'downloadTemplate'])->name('developer.wilayah.template');
        Route::resource('developer/wilayah', \App\Http\Controllers\Developer\WilayahController::class)->names('developer.wilayah');
    });

    Route::get('/pc/dashboard', function () {
        return view('pc.dashboard');
    })->middleware('role:pc,developer')->name('pc.dashboard');

    Route::get('/mwc/dashboard', function () {
        return view('mwc.dashboard');
    })->middleware('role:mwc,developer')->name('mwc.dashboard');

    Route::get('/ranting/dashboard', function () {
        return view('ranting.dashboard');
    })->middleware('role:ranting,developer')->name('ranting.dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
