@props([
    'members',
])

<section class="min-w-0 max-w-full overflow-hidden rounded-xl border border-border bg-surface">
    <div class="flex items-center justify-between gap-3 border-b border-border px-4 py-4 sm:px-5">
        <h2 class="min-w-0 truncate text-sm font-semibold text-text">Membres récents</h2>
        <a href="{{ route('admin.enrollments.index') }}" class="shrink-0 text-xs font-medium text-muted hover:text-primary">
            Voir tout
        </a>
    </div>

    @if ($members->isEmpty())
        <p class="px-4 py-8 text-sm text-muted sm:px-5">Aucun membre.</p>
    @else
        <ul class="divide-y divide-border">
            @foreach ($members as $member)
                <li class="min-w-0">
                    <a
                        href="{{ route('admin.enrollments.show', $member) }}"
                        class="flex min-w-0 items-center gap-3 px-4 py-3.5 hover:bg-white/[0.03] sm:px-5"
                    >
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-bg font-display text-sm font-bold text-primary sm:h-10 sm:w-10">
                            {{ mb_strtoupper(mb_substr($member->name, 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-text">{{ $member->name }}</p>
                            <p class="mt-0.5 truncate text-xs text-muted">
                                @if ($member->username)
                                    {{ '@'.$member->username }}
                                @else
                                    {{ $member->email }}
                                @endif
                                <span class="text-muted/70"> · {{ $member->created_at->format('d/m') }}</span>
                            </p>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</section>
