<x-layouts.admin title="Programmes">
    <div class="space-y-6">
        <header class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="font-display text-2xl font-bold uppercase tracking-wide">Programmes</h1>

            <a
                href="{{ route('admin.programs.create') }}"
                class="inline-flex min-h-11 items-center gap-2 rounded-lg bg-primary px-4 font-display text-xs font-bold uppercase tracking-wider text-bg transition-colors hover:bg-primary-hover"
            >
                <i class="fa-solid fa-plus text-[11px]"></i>
                Nouveau programme
            </a>
        </header>

        <div class="flex flex-wrap items-center gap-3">
            <form method="GET" class="flex min-w-0 flex-1 gap-2">
                @if ($archived)
                    <input type="hidden" name="archived" value="1">
                @endif
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

            <div class="flex gap-1 rounded-lg border border-border bg-surface p-1 text-sm">
                <a
                    href="{{ route('admin.programs.index', array_filter(['q' => $search ?: null])) }}"
                    class="rounded-md px-3 py-1.5 {{ ! $archived ? 'bg-primary/10 text-primary' : 'text-muted hover:text-text' }}"
                >
                    Catalogue
                </a>
                <a
                    href="{{ route('admin.programs.index', array_filter(['archived' => 1, 'q' => $search ?: null])) }}"
                    class="rounded-md px-3 py-1.5 {{ $archived ? 'bg-primary/10 text-primary' : 'text-muted hover:text-text' }}"
                >
                    Archives
                </a>
            </div>
        </div>

        <section class="overflow-hidden rounded-xl border border-border bg-surface">
            @if ($programs->isEmpty())
                <p class="px-5 py-10 text-sm text-muted">
                    {{ $archived ? 'Aucun programme archivé.' : 'Aucun programme.' }}
                </p>
            @else
                <ul class="divide-y divide-border">
                    @foreach ($programs as $program)
                        <li class="flex items-center gap-4 px-4 py-3.5">
                            @if ($program->imageUrl())
                                <img src="{{ $program->imageUrl() }}" alt="" class="h-12 w-12 shrink-0 rounded-lg object-cover">
                            @else
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-bg text-muted">
                                    <i class="fa-solid fa-calendar-check text-sm" aria-hidden="true"></i>
                                </div>
                            @endif

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-text">{{ $program->title }}</p>
                                <p class="mt-0.5 truncate text-xs tabular-nums text-muted">
                                    {{ $program->starts_on->format('d/m/Y') }} au {{ $program->ends_on->format('d/m/Y') }}
                                    · {{ $program->schedule_count }} {{ $program->schedule_count > 1 ? 'séances' : 'séance' }}
                                </p>
                            </div>

                            <span class="hidden shrink-0 text-xs font-medium sm:inline {{ $program->status === \App\Enums\ProgramStatus::Open ? 'text-primary' : 'text-muted' }}">
                                {{ $program->status->label() }}
                            </span>

                            <div class="flex shrink-0 items-center gap-2">
                                @if ($archived)
                                    <form method="POST" action="{{ route('admin.programs.restore', $program) }}">
                                        @csrf
                                        <button type="submit" class="min-h-11 px-2 text-sm font-medium text-text hover:text-primary">
                                            Restaurer
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.programs.edit', $program) }}" class="min-h-11 px-2 text-sm font-medium text-text hover:text-primary">
                                        Modifier
                                    </a>
                                    <form method="POST" action="{{ route('admin.programs.destroy', $program) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="min-h-11 px-2 text-sm font-medium text-muted hover:text-danger">
                                            Archiver
                                        </button>
                                    </form>
                                @endif
                            </div>
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
