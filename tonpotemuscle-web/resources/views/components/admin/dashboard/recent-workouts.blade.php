@props([
    'workouts',
])

<x-admin.dashboard.section-card
    title="Séances récentes"
    :href="route('admin.workouts.index')"
    empty-text="Aucune séance."
    :is-empty="$workouts->isEmpty()"
>
    <ul class="divide-y divide-border px-5">
        @foreach ($workouts as $workout)
            <li class="flex items-center justify-between gap-4 py-3.5">
                <div class="min-w-0">
                    <p class="truncate text-sm font-medium text-text">{{ $workout->title }}</p>
                    <p class="mt-0.5 text-xs tabular-nums text-muted">
                        {{ $workout->durationMinutes() }} min
                        @if ($workout->difficulty)
                            · {{ $workout->difficulty }}
                        @endif
                    </p>
                </div>

                <span class="shrink-0 text-xs text-muted">
                    {{ $workout->created_at->diffForHumans() }}
                </span>
            </li>
        @endforeach
    </ul>
</x-admin.dashboard.section-card>
