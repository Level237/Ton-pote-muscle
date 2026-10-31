@php
    $moreOpen = request()->routeIs('admin.enrollments.*', 'admin.rankings.*');
@endphp

<div class="lg:hidden">
    <input id="admin-more" type="checkbox" class="peer hidden">

    <label
        for="admin-more"
        class="pointer-events-none fixed inset-0 z-40 hidden bg-black/60 peer-checked:!block peer-checked:pointer-events-auto"
    ></label>

    <div class="fixed inset-x-3 bottom-20 z-50 hidden overflow-hidden rounded-xl border border-border bg-surface shadow-lg peer-checked:!block">
        <a href="{{ route('admin.enrollments.index') }}" class="flex items-center gap-3 px-4 py-3.5 text-sm {{ request()->routeIs('admin.enrollments.*') ? 'text-primary' : 'text-text' }}">
            <i class="fa-solid fa-user-group w-4 text-center text-xs text-muted"></i>
            Inscrits
        </a>
        <a href="{{ route('admin.rankings.index') }}" class="flex items-center gap-3 border-t border-border px-4 py-3.5 text-sm {{ request()->routeIs('admin.rankings.*') ? 'text-primary' : 'text-text' }}">
            <i class="fa-solid fa-trophy w-4 text-center text-xs text-muted"></i>
            Classements
        </a>
        <form method="POST" action="{{ route('admin.logout') }}" class="border-t border-border">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 px-4 py-3.5 text-left text-sm text-text">
                <i class="fa-solid fa-right-from-bracket w-4 text-center text-xs text-muted"></i>
                Déconnexion
            </button>
        </form>
    </div>

    <nav class="fixed inset-x-0 bottom-0 z-50 border-t border-border bg-surface pb-[env(safe-area-inset-bottom)]">
        <div class="flex items-stretch">
            <x-admin.mobile-tab href="{{ route('admin.dashboard') }}" icon="fa-solid fa-house" :active="request()->routeIs('admin.dashboard')">
                Home
            </x-admin.mobile-tab>
            <x-admin.mobile-tab href="{{ route('admin.exercises.index') }}" icon="fa-solid fa-dumbbell" :active="request()->routeIs('admin.exercises.*')">
                Exercices
            </x-admin.mobile-tab>
            <x-admin.mobile-tab href="{{ route('admin.programs.index') }}" icon="fa-solid fa-calendar-check" :active="request()->routeIs('admin.programs.*')">
                Programmes
            </x-admin.mobile-tab>
            <x-admin.mobile-tab href="{{ route('admin.workouts.index') }}" icon="fa-solid fa-stopwatch" :active="request()->routeIs('admin.workouts.*')">
                Séances
            </x-admin.mobile-tab>
            <x-admin.mobile-tab tag="label" for="admin-more" icon="fa-solid fa-ellipsis" :active="$moreOpen">
                Plus
            </x-admin.mobile-tab>
        </div>
    </nav>
</div>
