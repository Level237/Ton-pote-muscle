@props([
    'logs',
])

<section class="min-w-0 max-w-full overflow-hidden rounded-xl border border-border bg-surface">
    <div class="flex items-center justify-between gap-3 border-b border-border px-4 py-4 sm:px-5">
        <h2 class="truncate text-sm font-semibold text-text">Séances terminées</h2>
    </div>

    @if ($logs->isEmpty())
        <p class="px-4 py-8 text-sm text-muted sm:px-5">Aucune séance terminée pour le moment.</p>
    @else
        <ul class="divide-y divide-border">
            @foreach ($logs as $log)
                @php
                    $member = $log->member;
                    $fresh = $log->completed_at?->gt(now()->subMinutes(30));
                    $when = $log->completed_at;
                    $whenLabel = $when?->isToday()
                        ? $when->format('H:i')
                        : $when?->format('d/m · H:i');
                @endphp
                <li class="min-w-0">
                    <a
                        href="{{ $member ? route('admin.enrollments.show', $member) : '#' }}"
                        class="flex min-w-0 items-start gap-3 px-4 py-3.5 hover:bg-white/[0.03] sm:items-center sm:px-5"
                    >
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-bg text-xs font-semibold text-primary sm:mt-0 sm:h-10 sm:w-10">
                            {{ mb_strtoupper(mb_substr($member?->name ?? '?', 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex min-w-0 items-start justify-between gap-2">
                                <p class="min-w-0 truncate text-sm font-medium text-text">
                                    {{ $member?->name }}
                                </p>
                                @if ($fresh)
                                    <span class="shrink-0 rounded-full bg-primary/15 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-primary">
                                        Nouveau
                                    </span>
                                @endif
                            </div>
                            <p class="mt-0.5 truncate text-sm text-muted">
                                a terminé {{ $log->workout?->title ?? 'une séance' }}
                            </p>
                            <p class="mt-0.5 truncate text-xs text-muted">
                                {{ $log->mode === 'live' ? 'Live' : 'Déclarée' }}
                                @if ($whenLabel)
                                    · {{ $whenLabel }}
                                @endif
                            </p>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</section>
