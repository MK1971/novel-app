<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('chapter:editing-deadline-reminders')->dailyAt('08:00');
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        // Applied globally so /up (health) and web routes all send baseline headers.
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        // Apple OAuth POST callback; only needed when APPLE_SIGN_IN_ENABLED=true and Apple is used.
        $middleware->validateCsrfTokens(except: [
            'auth/apple/callback',
        ]);
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
