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
        \Illuminate\Support\Facades\Blade::component('layouts.app', 'app-layout');
        \Illuminate\Support\Facades\Blade::component('layouts.guest', 'guest-layout');

        Gate::define('admin', fn ($user) => $user->email === env('ADMIN_EMAIL', 'admin@example.com'));

        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
            $topLeader = \App\Models\User::where('email', '!=', $adminEmail)
                ->orderByDesc('points')
                ->first();
            $view->with('topLeader', $topLeader);
        });
    }
}
