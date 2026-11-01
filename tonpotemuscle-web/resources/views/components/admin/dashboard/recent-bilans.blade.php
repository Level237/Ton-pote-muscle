@props([
    'bilans',
])

<x-admin.dashboard.section-card
    title="Bilans"
    :href="route('admin.bilans.index')"
    empty-text="Aucun bilan."
    :is-empty="$bilans->isEmpty()"
>
    <ul class="divide-y divide-border px-5">
        @foreach ($bilans as $bilan)
            <li class="flex items-center justify-between gap-4 py-3.5">
                <div class="min-w-0">
                    <p class="truncate text-sm font-medium text-text">{{ $bilan->title }}</p>
                    <p class="mt-0.5 text-xs text-muted">
                        {{ $bilan->questions_count }} {{ $bilan->questions_count > 1 ? 'questions' : 'question' }}
                    </p>
                </div>
            </li>
        @endforeach
    </ul>
</x-admin.dashboard.section-card>
