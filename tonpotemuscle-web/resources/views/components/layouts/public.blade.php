@props([
    'title' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name', 'Ton Pote Musclé') }}</title>
        <x-font-awesome />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-bg font-sans text-text antialiased">
        <header class="sticky top-0 z-10 border-b border-border bg-surface">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 lg:px-6">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('assets/img/logo.webp') }}" alt="Ton Pote Musclé" class="h-8 w-auto">
                </a>
                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="text-sm font-medium text-muted hover:text-primary">
                        Espace
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-muted hover:text-primary">
                        Connexion
                    </a>
                @endauth
            </div>
        </header>
        <main class="mx-auto max-w-6xl px-4 py-8 lg:px-6 lg:py-12">
            {{ $slot }}
        </main>
    </body>
</html>
