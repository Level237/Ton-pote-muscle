@props([
    'title' => null,
    'heading' => null,
    'subtitle' => null,
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
    <body class="min-h-dvh bg-bg font-sans text-text antialiased">
        <div class="mx-auto flex min-h-dvh w-full max-w-md flex-col justify-center px-4 py-12 sm:px-6">
            <div class="mb-6 flex items-center justify-between">
                <a href="{{ route('home') }}" class="inline-flex items-center">
                    <img src="{{ asset('assets/img/logo.webp') }}" alt="Ton Pote Musclé" class="h-9 w-auto">
                </a>
                @if ($subtitle)
                    <span class="text-xs font-medium uppercase tracking-wider text-muted">{{ $subtitle }}</span>
                @endif
            </div>

            <div class="rounded-2xl border border-border bg-surface p-6 sm:p-8">
                @if ($heading)
                    <h1 class="mb-6 font-display text-2xl font-bold uppercase tracking-wide text-text">
                        {{ $heading }}
                    </h1>
                @endif

                {{ $slot }}
            </div>
        </div>
    </body>
</html>
