<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\AccountObserver;
use App\Models\Account;

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
        //
        Account::observe(AccountObserver::class);
    }
}
