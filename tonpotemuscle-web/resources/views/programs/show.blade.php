<x-layouts.public :title="$program->title">
    <article class="mx-auto max-w-3xl space-y-8">
        @if ($program->imageUrl())
            <div class="relative overflow-hidden rounded-[20px]">
                <img src="{{ $program->imageUrl() }}" alt="" class="h-56 w-full object-cover lg:h-72">
                <div class="absolute inset-0 bg-black/45"></div>
            </div>
        @endif

        <header class="space-y-3">
            <p class="text-xs font-medium uppercase tracking-wider text-muted">
                {{ $program->status->label() }}
                @if ($program->levelLabel())
                    · {{ $program->levelLabel() }}
                @endif
            </p>
            <h1 class="font-display text-3xl font-bold uppercase tracking-wide lg:text-4xl">{{ $program->title }}</h1>
            <p class="tabular-nums text-sm text-muted">
                {{ $program->starts_on->format('d/m/Y') }} au {{ $program->ends_on->format('d/m/Y') }}
            </p>
        </header>

        @if ($program->description)
            <p class="text-base leading-relaxed text-text">{{ $program->description }}</p>
        @endif

        @if ($program->objective)
            <p class="text-sm text-muted">Objectif : {{ $program->objective }}</p>
        @endif

        <section>
            <h2 class="mb-3 text-sm font-medium text-muted">Calendrier</h2>
            <ul class="divide-y divide-border overflow-hidden rounded-xl border border-border bg-surface">
                @foreach ($program->schedule as $slot)
                    <li class="flex items-center justify-between gap-4 px-4 py-3.5">
                        <span class="text-sm tabular-nums text-muted">Jour {{ $slot->day_number }}</span>
                        <span class="text-sm font-medium text-text">{{ $slot->workout?->title }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    </article>
</x-layouts.public>
