<?php

namespace App\Providers;

use App\Services\Pengaturan;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Pengaturan::class);
    }

    public function boot(): void
    {
        // tersedia sebagai $set di semua view (termasuk PDF)
        View::share('set', $this->app->make(Pengaturan::class));
    }
}