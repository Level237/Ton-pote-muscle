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
    <body class="min-h-screen bg-bg font-sans text-text antialiased">
        <div class="pointer-events-none fixed inset-0 bg-[radial-gradient(ellipse_at_50%_30%,rgba(230,244,25,0.07),transparent_52%)]"></div>
        <div class="relative flex min-h-screen flex-col items-center justify-center px-6 py-16">
            <a href="{{ route('home') }}" class="mb-12 block">
                <img src="{{ asset('assets/img/logo.webp') }}" alt="Ton Pote Musclé" class="h-12 w-auto">
            </a>
            {{ $slot }}
        </div>
    </body>
</html>
