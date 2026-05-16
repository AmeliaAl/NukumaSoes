<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\FakturPembelian;  // ← TAMBAHKAN INI
use App\Observers\FakturPembelianObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FakturPembelian::observe(FakturPembelianObserver::class);
    }
}
