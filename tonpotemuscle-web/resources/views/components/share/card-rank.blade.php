@props([
    'entry',
    'program',
])

<article class="relative overflow-hidden rounded-[24px] border border-border bg-surface px-6 py-8">
    <span class="absolute inset-y-0 left-0 w-1.5 bg-primary" aria-hidden="true"></span>
    <img src="{{ asset('assets/img/logo.webp') }}" alt="" class="h-7 w-auto">
    <p class="mt-8 text-[11px] font-bold uppercase tracking-[0.22em] text-primary">Classement officiel</p>
    <p class="mt-3 font-display text-7xl font-bold tabular-nums leading-none text-primary">
        {{ \App\Support\ShareCopy::ordinal((int) $entry->rank) }}
    </p>
    <h2 class="mt-4 font-display text-3xl font-bold uppercase leading-none tracking-wide text-text">
        {{ $program->title }}
    </h2>
    @if ($entry->username)
        <p class="mt-3 text-sm text-muted">{{ '@'.$entry->username }}</p>
    @endif
    <p class="mt-8 text-sm tabular-nums text-text">
        {{ $entry->points }} pts
        · {{ $entry->live_count }} live{{ $entry->live_count > 1 ? 's' : '' }}
    </p>
</article>
