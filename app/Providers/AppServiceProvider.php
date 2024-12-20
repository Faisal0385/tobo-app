<?php

namespace App\Providers;

use App\Models\Client;
use App\Observers\RegisterObserver;
use Illuminate\Support\ServiceProvider;

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
        Client::observe((RegisterObserver::class));
    }
}
