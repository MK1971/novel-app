<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'What\'s My Book Name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=nunito:400,600,700,800" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen antialiased" style="font-family: 'Nunito', sans-serif; background: #fff9f0; color: #2c2419;">
        <div class="min-h-screen flex flex-col">
            <div class="flex flex-1">
                @if(!isset($hideSidebar) || !$hideSidebar)
                    @include('layouts.sidebar')
                @endif
                <div class="flex-1">
                    {{ $slot }}
                </div>
            </div>
        </div>

        {{-- Auth modals for guests --}}
        @if (!auth()->check())
            @include('auth.modals')
        @endif
    </body>
</html>
