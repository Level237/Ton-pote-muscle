<x-layouts.admin title="Inscrits">
    <div class="space-y-6">
        <header class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="font-display text-2xl font-bold uppercase tracking-wide">Inscrits</h1>
        </header>

        <div class="flex flex-wrap items-center gap-3">
            <form method="GET" class="flex min-w-0 flex-1 gap-2">
                @if ($deactivated)
                    <input type="hidden" name="deactivated" value="1">
                @endif
                <label class="sr-only" for="q">Rechercher</label>
                <input
                    id="q"
                    name="q"
                    type="search"
                    value="{{ $search }}"
                    placeholder="Rechercher un inscrit"
                    class="min-h-11 w-full max-w-sm rounded-lg border border-border bg-surface px-3 text-sm text-text outline-none placeholder:text-muted/50 focus:border-primary focus:ring-2 focus:ring-primary"
                >
                <button type="submit" class="min-h-11 rounded-lg border border-border bg-surface px-4 text-sm font-medium text-text hover:border-primary hover:text-primary">
                    Chercher
                </button>
            </form>

            <div class="flex gap-1 rounded-lg border border-border bg-surface p-1 text-sm">
                <a
                    href="{{ route('admin.enrollments.index', array_filter(['q' => $search ?: null])) }}"
                    class="rounded-md px-3 py-1.5 {{ ! $deactivated ? 'bg-primary/10 text-primary' : 'text-muted hover:text-text' }}"
                >
                    Actifs
                </a>
                <a
                    href="{{ route('admin.enrollments.index', array_filter(['deactivated' => 1, 'q' => $search ?: null])) }}"
                    class="rounded-md px-3 py-1.5 {{ $deactivated ? 'bg-primary/10 text-primary' : 'text-muted hover:text-text' }}"
                >
                    Désactivés
                </a>
            </div>
        </div>

        <section class="overflow-hidden rounded-xl border border-border bg-surface">
            @if ($members->isEmpty())
                <p class="px-5 py-10 text-sm text-muted">
                    {{ $deactivated ? 'Aucun compte désactivé.' : 'Aucun inscrit.' }}
                </p>
            @else
                <ul class="divide-y divide-border">
                    @foreach ($members as $member)
                        <li class="flex items-center gap-4 px-4 py-3.5">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-bg font-display text-sm font-bold uppercase text-muted">
                                {{ mb_substr($member->name, 0, 1) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-text">
                                    {{ $member->name }}
                                    @if ($member->username)
                                        <span class="font-normal text-muted">{{ '@'.$member->username }}</span>
                                    @endif
                                </p>
                                <p class="mt-0.5 truncate text-xs text-muted">
                                    {{ $member->email }}
                                    · {{ $member->created_at->format('d/m/Y') }}
                                </p>
                            </div>

                            <div class="flex shrink-0 items-center gap-2">
                                @if ($deactivated)
                                    <form method="POST" action="{{ route('admin.enrollments.activate', $member) }}">
                                        @csrf
                                        <button type="submit" class="min-h-11 px-2 text-sm font-medium text-text hover:text-primary">
                                            Réactiver
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.enrollments.deactivate', $member) }}">
                                        @csrf
                                        <button type="submit" class="min-h-11 px-2 text-sm font-medium text-muted hover:text-danger">
                                            Désactiver
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        @if ($members->hasPages())
            <div class="text-sm text-muted">
                {{ $members->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
