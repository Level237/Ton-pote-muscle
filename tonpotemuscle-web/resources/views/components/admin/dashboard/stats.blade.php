@props([
    'stats' => [],
])

<section aria-label="Statistiques">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $stat)
            <x-admin.dashboard.stat-card
                :label="$stat['label']"
                :value="$stat['value']"
                :icon="$stat['icon']"
            />
        @endforeach
    </div>
</section>
