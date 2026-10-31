@props([
    'programs',
])

<x-admin.dashboard.section-card
    title="Programmes récents"
    :href="route('admin.programs.index')"
    empty-text="Aucun programme."
    :is-empty="$programs->isEmpty()"
>
    <ul class="divide-y divide-border px-5">
        @foreach ($programs as $program)
            <li class="flex items-center justify-between gap-4 py-3.5">
                <div class="min-w-0">
                    <p class="truncate text-sm font-medium text-text">{{ $program->title }}</p>
                    <p class="mt-0.5 text-xs tabular-nums text-muted">
                        {{ $program->starts_on->format('d/m/Y') }} au {{ $program->ends_on->format('d/m/Y') }}
                    </p>
                </div>

                <span class="shrink-0 text-xs font-medium {{ $program->status === \App\Enums\ProgramStatus::Open ? 'text-primary' : 'text-muted' }}">
                    {{ $program->status->label() }}
                </span>
            </li>
        @endforeach
    </ul>
</x-admin.dashboard.section-card>
