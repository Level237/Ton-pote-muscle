<x-layouts.admin title="Classements">
    <div class="space-y-6">
        <header class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="font-display text-2xl font-bold uppercase tracking-wide">Classements</h1>
        </header>

        <form method="GET" class="flex min-w-0 gap-2">
            <label class="sr-only" for="q">Rechercher</label>
            <input
                id="q"
                name="q"
                type="search"
                value="{{ $search }}"
                placeholder="Rechercher un programme"
                class="min-h-11 w-full max-w-sm rounded-lg border border-border bg-surface px-3 text-sm text-text outline-none placeholder:text-muted/50 focus:border-primary focus:ring-2 focus:ring-primary"
            >
            <button type="submit" class="min-h-11 rounded-lg border border-border bg-surface px-4 text-sm font-medium text-text hover:border-primary hover:text-primary">
                Chercher
            </button>
        </form>

        <section class="overflow-hidden rounded-xl border border-border bg-surface">
            @if ($programs->isEmpty())
                <p class="px-5 py-10 text-sm text-muted">Aucun programme ouvert ou clôturé.</p>
            @else
                <ul class="divide-y divide-border">
                    @foreach ($programs as $program)
                        <li class="flex items-center gap-4 px-4 py-3.5">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-bg text-muted">
                                <i class="fa-solid fa-trophy text-sm" aria-hidden="true"></i>
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-text">{{ $program->title }}</p>
                                <p class="mt-0.5 truncate text-xs tabular-nums text-muted">
                                    {{ $program->starts_on->format('d/m/Y') }} au {{ $program->ends_on->format('d/m/Y') }}
                                    · {{ $program->enrollments_count }} {{ $program->enrollments_count > 1 ? 'inscrits' : 'inscrit' }}
                                </p>
                            </div>

                            <span class="hidden shrink-0 text-xs font-medium sm:inline {{ $program->rankingSnapshot ? 'text-muted' : 'text-primary' }}">
                                {{ $program->rankingSnapshot ? 'Publié' : 'Provisoire' }}
                            </span>

                            <a href="{{ route('admin.rankings.show', $program) }}" class="min-h-11 px-2 text-sm font-medium text-text hover:text-primary">
                                Voir
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        @if ($programs->hasPages())
            <div class="text-sm text-muted">
                {{ $programs->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
