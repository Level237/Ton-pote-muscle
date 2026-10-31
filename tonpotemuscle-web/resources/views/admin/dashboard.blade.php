<x-layouts.admin title="Tableau de bord">
    <div class="space-y-8">
        <header class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="font-display text-2xl font-bold uppercase tracking-wide">Tableau de bord</h1>

            <a
                href="{{ route('admin.programs.index') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 font-display text-xs font-bold uppercase tracking-wider text-bg transition-colors hover:bg-primary-hover"
            >
                <i class="fa-solid fa-plus text-[11px]"></i>
                <span>Nouveau programme</span>
            </a>
        </header>

        <x-admin.dashboard.stats :stats="$stats" />

        <x-admin.dashboard.quick-links :links="$quickLinks" />

        <div class="grid gap-6 lg:grid-cols-2">
            <x-admin.dashboard.recent-programs :programs="$recentPrograms" />
            <x-admin.dashboard.recent-workouts :workouts="$recentWorkouts" />
        </div>

        <x-admin.dashboard.recent-members :members="$recentMembers" />
    </div>
</x-layouts.admin>
