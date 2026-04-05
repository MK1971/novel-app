<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

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
        // Apple driver stays registered for when APPLE_SIGN_IN_ENABLED=true (no code removal on defer).
        Event::listen(function (SocialiteWasCalled $event): void {
            $event->extendSocialite('apple', \SocialiteProviders\Apple\Provider::class);
        });

        Blade::component('layouts.app', 'app-layout');
        Blade::component('layouts.guest', 'guest-layout');

        Gate::define('admin', function (User $user) {
            return $user->is_admin === true || $user->email === env('ADMIN_EMAIL', 'admin@example.com');
        });

        View::composer(['layouts.app', 'layouts.guest'], function ($view) {
            $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
            $topLeader = Cache::remember('layout.top_leader.v2', 60, function () use ($adminEmail) {
                return User::query()
                    ->where('email', '!=', $adminEmail)
                    ->where('leaderboard_visible', true)
                    ->orderByDesc('points')
                    ->first();
            });
            $view->with('topLeader', $topLeader);
        });

        View::composer(['layouts.app', 'layouts.guest'], function ($view) {
            $unreadNotificationCount = 0;
            if (Auth::check()) {
                $unreadNotificationCount = Auth::user()->notifications()->whereNull('read_at')->count();
            }
            $view->with('unreadNotificationCount', $unreadNotificationCount);
        });
    }
}
