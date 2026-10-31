@props([
    'title' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? config('app.name', 'Ton Pote Musclé') }}</title>
        <x-font-awesome />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-bg text-text antialiased">
        <header class="sticky top-0 z-10 border-b border-border bg-surface">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('assets/img/logo.webp') }}" alt="Ton Pote Musclé" class="h-8 w-auto">
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-semibold text-muted hover:text-primary">
                        Déconnexion
                    </button>
                </form>
            </div>
        </header>
        <main class="mx-auto max-w-6xl px-6 py-10">
            {{ $slot }}
        </main>
    </body>
</html>
