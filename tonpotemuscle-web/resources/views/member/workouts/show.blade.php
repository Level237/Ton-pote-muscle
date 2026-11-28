<x-layouts.member :title="$workout->title">
    <div class="pb-16">
        <a href="{{ route('dashboard') }}" class="text-sm text-muted hover:text-text">← Retour</a>

        <x-cover :src="$workout->thumbnailUrl()" icon="fa-solid fa-stopwatch" size="hero" class="mt-4" />
        @if ($isCatchUp ?? false)
            <p class="mt-3 text-[11px] font-bold uppercase tracking-wider text-primary">
                Rattrapage · prévue le {{ $item->planned_on->format('d/m/Y') }}
            </p>
        @endif
        <h1 class="{{ ($isCatchUp ?? false) ? 'mt-1' : 'mt-3' }} text-2xl font-bold text-text">{{ $workout->title }}</h1>
        <p class="mt-1 text-sm text-muted">
            {{ $workout->durationMinutes() }} min
            · {{ $workout->items->count() }} exercice{{ $workout->items->count() > 1 ? 's' : '' }}
            @if ($workout->category)
                · {{ $workout->category->title }}
            @endif
        </p>

        @if ($workout->notes)
            <p class="mt-3 text-sm text-muted">{{ $workout->notes }}</p>
        @endif

        <ol class="mt-6 divide-y divide-border border-y border-border">
            @foreach ($workout->items as $row)
                <li class="flex items-center gap-4 py-3.5">
                    @if ($row->exercise?->gifUrl())
                        <img
                            src="{{ $row->exercise->gifUrl() }}"
                            alt=""
                            class="h-16 w-16 shrink-0 rounded-lg bg-surface object-contain"
                        >
                    @elseif ($row->exercise?->demoUrl())
                        <a
                            href="{{ $row->exercise->demoUrl() }}"
                            target="_blank"
                            rel="noreferrer"
                            class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg bg-surface text-primary"
                            aria-label="Voir la démo"
                        >
                            <i class="fa-solid fa-play text-sm"></i>
                        </a>
                    @endif
                    <div class="min-w-0 flex-1">
                        <p class="text-base font-medium text-text">{{ $row->exercise?->title }}</p>
                        <p class="mt-0.5 text-sm text-muted">{{ $item->loadLabel($row) }} · repos {{ $row->restLabel() }}</p>
                        @if ($row->exercise?->demoUrl() && ! $row->exercise?->gifUrl())
                            <a
                                href="{{ $row->exercise->demoUrl() }}"
                                target="_blank"
                                rel="noreferrer"
                                class="mt-1 inline-block text-sm text-primary hover:text-primary-hover"
                            >
                                Voir la démo
                            </a>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>

        @if (session('error'))
            <p class="mt-6 rounded-xl border border-danger/30 bg-danger/10 px-4 py-3 text-sm text-danger">
                {{ session('error') }}
            </p>
        @endif

        @if ($done)
            <p class="mt-6 text-sm text-muted">Séance faite.</p>
            <a
                href="{{ route('member.workouts.recap', $item) }}"
                class="mt-4 flex min-h-12 w-full items-center justify-center rounded-xl bg-primary px-4 font-display text-sm font-bold uppercase tracking-wider text-bg transition-colors hover:bg-primary-hover"
            >
                Partager
            </a>
        @elseif (($isCatchUp ?? false) && ($programClosed ?? false))
            <p class="mt-6 rounded-xl border border-border bg-surface px-4 py-3 text-sm text-muted">
                Ce programme est clôturé : cette séance ne peut plus être rattrapée.
            </p>
        @elseif ($catchUpBlocked ?? false)
            <p class="mt-6 rounded-xl border border-border bg-surface px-4 py-3 text-sm text-muted">
                Tu as déjà rattrapé une séance aujourd’hui. Reviens demain pour la suivante.
            </p>
        @else
            <form method="POST" action="{{ route('member.workouts.start', $item) }}" class="mt-6">
                @csrf
                <button
                    type="submit"
                    class="flex min-h-12 w-full items-center justify-center rounded-xl bg-primary px-4 text-base font-bold text-bg transition-colors hover:bg-primary-hover"
                >
                    {{ ($isCatchUp ?? false) ? 'Rattraper cette séance' : 'Commencez' }}
                </button>
            </form>
        @endif
    </div>
</x-layouts.member>
