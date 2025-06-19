<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Investment;
use App\Observers\InvestmentObserver;

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
        Investment::observe(InvestmentObserver::class);
    }
}
