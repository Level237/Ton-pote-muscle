@props([
    'title' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? 'Tableau de bord' }}</title>
        <x-font-awesome />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-bg font-sans text-text antialiased">
        <x-admin.top-bar />

        <div class="flex min-h-[calc(100vh-3.5rem)]">
            <aside class="sticky top-14 hidden h-[calc(100vh-3.5rem)] w-56 shrink-0 flex-col border-r border-border bg-surface lg:flex">
                <nav class="flex flex-1 flex-col gap-1 px-3 py-4">
                    <x-admin.nav-link href="{{ route('admin.dashboard') }}" icon="fa-solid fa-grip" :active="request()->routeIs('admin.dashboard')">
                        Tableau de bord
                    </x-admin.nav-link>
                    <x-admin.nav-link href="{{ route('admin.exercises.index') }}" icon="fa-solid fa-dumbbell" :active="request()->routeIs('admin.exercises.*')">
                        Exercices
                    </x-admin.nav-link>
                    <x-admin.nav-link href="{{ route('admin.workouts.index') }}" icon="fa-solid fa-stopwatch" :active="request()->routeIs('admin.workouts.*')">
                        Séances
                    </x-admin.nav-link>
                    <x-admin.nav-link href="{{ route('admin.programs.index') }}" icon="fa-solid fa-calendar-check" :active="request()->routeIs('admin.programs.*')">
                        Programmes
                    </x-admin.nav-link>
                    <x-admin.nav-link href="{{ route('admin.enrollments.index') }}" icon="fa-solid fa-user-group" :active="request()->routeIs('admin.enrollments.*')">
                        Inscrits
                    </x-admin.nav-link>
                    <x-admin.nav-link href="{{ route('admin.rankings.index') }}" icon="fa-solid fa-trophy" :active="request()->routeIs('admin.rankings.*')">
                        Classements
                    </x-admin.nav-link>
                </nav>

                <div class="border-t border-border p-3">
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm font-medium text-muted transition-colors hover:bg-white/[0.04] hover:text-text">
                            <i class="fa-solid fa-right-from-bracket w-4 text-center text-xs"></i>
                            <span>Déconnexion</span>
                        </button>
                    </form>
                </div>
            </aside>

                <main class="min-w-0 flex-1 px-4 py-6 pb-24 lg:px-12 lg:py-10 lg:pb-10">
                <div class="mx-auto max-w-6xl">
                    @if (session('status'))
                        <p class="mb-6 rounded-lg border border-primary/20 bg-primary/10 px-4 py-3 text-sm text-primary" role="status">
                            {{ session('status') }}
                        </p>
                    @endif
                    {{ $slot }}
                </div>
            </main>
        </div>

        <x-admin.mobile-bar />
    </body>
</html>
