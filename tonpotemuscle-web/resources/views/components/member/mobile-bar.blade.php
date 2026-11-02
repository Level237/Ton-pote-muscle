<nav class="fixed inset-x-0 bottom-0 z-50 border-t border-border bg-surface pb-[env(safe-area-inset-bottom)] lg:hidden">
    <div class="flex items-stretch">
        <x-member.mobile-tab href="{{ route('dashboard') }}" icon="fa-solid fa-house" :active="request()->routeIs('dashboard')">
            Accueil
        </x-member.mobile-tab>
        <x-member.mobile-tab href="{{ route('member.programs.index') }}" icon="fa-solid fa-calendar-check" :active="request()->routeIs('member.programs.*')">
            Mes programmes
        </x-member.mobile-tab>
        <x-member.mobile-tab href="{{ route('member.bilans.index') }}" icon="fa-solid fa-clipboard-list" :active="request()->routeIs('member.bilans.*')">
            Bilans
        </x-member.mobile-tab>
        <x-member.mobile-tab href="{{ route('member.profile.edit') }}" icon="fa-solid fa-user" :active="request()->routeIs('member.profile.*')">
            Profil
        </x-member.mobile-tab>
    </div>
</nav>
