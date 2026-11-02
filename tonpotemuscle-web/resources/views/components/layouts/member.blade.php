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
        <header class="sticky top-0 z-50 border-b border-border bg-surface">
            <div class="flex h-14 items-center px-4 lg:px-6">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center">
                    <img src="{{ asset('assets/img/logo.webp') }}" alt="Ton Pote Musclé" class="h-7 w-auto">
                </a>
            </div>
        </header>

        <div class="flex min-h-[calc(100vh-3.5rem)]">
            <aside class="sticky top-14 hidden h-[calc(100vh-3.5rem)] w-56 shrink-0 flex-col border-r border-border bg-surface lg:flex">
                <nav class="flex flex-1 flex-col gap-1 px-3 py-4">
                    <x-member.nav-link href="{{ route('dashboard') }}" icon="fa-solid fa-house" :active="request()->routeIs('dashboard')">
                        Accueil
                    </x-member.nav-link>
                    <x-member.nav-link href="{{ route('member.programs.index') }}" icon="fa-solid fa-calendar-check" :active="request()->routeIs('member.programs.*')">
                        Mes programmes
                    </x-member.nav-link>
                    <x-member.nav-link href="{{ route('member.bilans.index') }}" icon="fa-solid fa-clipboard-list" :active="request()->routeIs('member.bilans.*')">
                        Bilans
                    </x-member.nav-link>
                    <x-member.nav-link href="{{ route('member.profile.edit') }}" icon="fa-solid fa-user" :active="request()->routeIs('member.profile.*')">
                        Profil
                    </x-member.nav-link>
                </nav>

                <div class="border-t border-border p-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm font-medium text-muted transition-colors hover:bg-white/[0.04] hover:text-text">
                            <i class="fa-solid fa-right-from-bracket w-4 text-center text-xs"></i>
                            <span>Déconnexion</span>
                        </button>
                    </form>
                </div>
            </aside>

            <main class="min-w-0 flex-1 px-4 py-6 pb-24 lg:px-12 lg:py-10 lg:pb-10">
                <div class="mx-auto max-w-xl">
                    @if (session('status'))
                        <p class="mb-6 rounded-lg border border-primary/20 bg-primary/10 px-4 py-3 text-sm text-primary" role="status">
                            {{ session('status') }}
                        </p>
                    @endif
                    {{ $slot }}
                </div>
            </main>
        </div>

        <x-member.mobile-bar />
    </body>
</html>
