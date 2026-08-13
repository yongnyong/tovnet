<?php

namespace App\Providers;

use App\Models\Usim;
use App\Observers\UsimObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Usim::observe(UsimObserver::class);
        Paginator::useBootstrap();
    }
}
