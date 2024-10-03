<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('App\Repositories\Team\TeamRepositoryInterface', 'App\Repositories\Team\TeamRepository');
        $this->app->bind('App\Repositories\League\LeagueRepositoryInterface', 'App\Repositories\League\LeagueRepository');
        $this->app->bind('App\Repositories\Match\MatchRepositoryInterface', 'App\Repositories\Match\MatchRepository');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
