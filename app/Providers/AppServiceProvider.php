<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\MyClient;
use App\Observers\MyClientObserver;

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
        MyClient::observe(MyClientObserver::class);
    }
}
