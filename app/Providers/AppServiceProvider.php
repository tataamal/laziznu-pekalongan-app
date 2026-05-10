<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\KoinNuTransaction;
use App\Observers\KoinNuTransactionObserver;
use App\Repositories\KoinNuTransactionRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(KoinNuTransactionRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        KoinNuTransaction::observe(KoinNuTransactionObserver::class);
    }
}
