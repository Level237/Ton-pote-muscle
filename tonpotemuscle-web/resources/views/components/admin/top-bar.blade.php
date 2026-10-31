@php
    $user = auth()->user();
    $initials = strtoupper(mb_substr($user->name ?: $user->username, 0, 1));
@endphp

<header class="sticky top-0 z-50 overflow-visible border-b border-border bg-surface">
    <div class="flex h-14 items-center justify-between px-4 lg:px-6">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center">
            <img src="{{ asset('assets/img/logo.webp') }}" alt="Ton Pote Musclé" class="h-7 w-auto">
        </a>

        <div class="relative">
            <input id="admin-account" type="checkbox" class="peer hidden">

            <label
                for="admin-account"
                class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-white/[0.06] text-xs font-semibold text-primary ring-1 ring-border"
                aria-label="Compte"
            >
                {{ $initials }}
            </label>

            <label
                for="admin-account"
                class="fixed inset-0 z-40 hidden peer-checked:!block"
            ></label>

            <div class="fixed right-4 top-14 z-[60] hidden w-48 overflow-hidden rounded-xl border border-border bg-surface shadow-lg peer-checked:!block">
                <a href="{{ route('admin.profile') }}" class="flex items-center gap-3 px-4 py-3 text-sm {{ request()->routeIs('admin.profile') ? 'text-primary' : 'text-text' }}">
                    <i class="fa-solid fa-user w-4 text-center text-xs text-muted"></i>
                    Profil
                </a>
                <form method="POST" action="{{ route('admin.logout') }}" class="border-t border-border">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm text-text">
                        <i class="fa-solid fa-right-from-bracket w-4 text-center text-xs text-muted"></i>
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
