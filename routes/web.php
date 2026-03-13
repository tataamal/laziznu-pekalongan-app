<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/developer/dashboard', function () {
        return view('developer.dashboard');
    })->middleware('role:developer')->name('developer.dashboard');

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
