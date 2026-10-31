@props([
    'stats' => [],
])

<section aria-label="Statistiques" class="-mx-4 md:mx-0">
    <div
        class="admin-stat-carousel ml-4 flex snap-x snap-mandatory gap-2 overflow-x-auto px-4 pb-1 md:grid md:grid-cols-2 md:gap-4 md:overflow-visible md:px-0 xl:grid-cols-4"
        tabindex="0"
    >
        @foreach ($stats as $stat)
            <div class="w-62 h-28 shrink-0 snap-start md:w-auto">
                <x-admin.dashboard.stat-card
                    :label="$stat['label']"
                    :value="$stat['value']"
                    :icon="$stat['icon']"
                />
            </div>
        @endforeach
    </div>
</section>
