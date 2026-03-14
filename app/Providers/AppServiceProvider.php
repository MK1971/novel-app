<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
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
        Gate::define('admin', fn ($user) => $user->email === env('ADMIN_EMAIL', 'admin@example.com'));

        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $topLeader = \App\Models\User::orderByDesc('points')->first();
            $view->with('topLeader', $topLeader);
        });
    }
}
