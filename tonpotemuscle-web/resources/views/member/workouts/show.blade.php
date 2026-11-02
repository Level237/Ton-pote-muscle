<x-layouts.member :title="$workout->title">
    <div class="pb-16">
        <a href="{{ route('dashboard') }}" class="text-sm text-muted hover:text-text">← Retour</a>

        <h1 class="mt-3 text-2xl font-bold text-text">{{ $workout->title }}</h1>
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
                        <p class="mt-0.5 text-sm text-muted">{{ $row->workLabel() }} · repos {{ $row->restLabel() }}</p>
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

        @if ($done)
            <p class="mt-6 text-sm text-muted">Séance faite.</p>
        @else
            <form method="POST" action="{{ route('member.workouts.start', $item) }}" class="mt-6">
                @csrf
                <button
                    type="submit"
                    class="flex min-h-12 w-full items-center justify-center rounded-xl bg-primary px-4 text-base font-bold text-bg transition-colors hover:bg-primary-hover"
                >
                    Commencez
                </button>
            </form>
        @endif
    </div>
</x-layouts.member>
