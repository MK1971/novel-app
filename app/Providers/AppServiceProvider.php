<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use App\Models\User;

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
        Blade::component('layouts.app', 'app-layout');
        Blade::component('layouts.guest', 'guest-layout');

        Gate::define('admin', function (User $user) {
            return $user->is_admin === true || $user->email === env('ADMIN_EMAIL', 'admin@example.com');
        });

        View::composer('*', function ($view) {
            $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
            $topLeader = User::where('email', '!=', $adminEmail)
                ->orderByDesc('points')
                ->first();
            $view->with('topLeader', $topLeader);
        });
    }
}
