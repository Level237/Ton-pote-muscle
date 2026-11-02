<x-layouts.member title="Bilans">
    <div class="space-y-6">
        <h1 class="font-display text-2xl font-bold uppercase tracking-wide">Bilans</h1>

        @if ($bilans->isEmpty())
            <p class="rounded-xl border border-border bg-surface px-5 py-10 text-sm text-muted">
                Aucun bilan à remplir pour le moment.
            </p>
        @else
            <ul class="divide-y divide-border overflow-hidden rounded-xl border border-border bg-surface">
                @foreach ($bilans as $bilan)
                    @php $done = $bilan->submissions->isNotEmpty(); @endphp
                    <li>
                        <a href="{{ route('member.bilans.show', $bilan) }}" class="flex items-center gap-4 px-4 py-3.5 hover:bg-white/[0.03]">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-bg text-muted">
                                <i class="fa-solid fa-clipboard-list text-sm" aria-hidden="true"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-text">{{ $bilan->title }}</p>
                                <p class="mt-0.5 truncate text-xs text-muted">
                                    {{ $bilan->questions_count }} {{ $bilan->questions_count > 1 ? 'questions' : 'question' }}
                                    · {{ $done ? 'Répondu' : 'À remplir' }}
                                </p>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-muted"></i>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-layouts.member>
