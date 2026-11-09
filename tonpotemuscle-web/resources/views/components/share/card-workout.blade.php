@props([
    'log',
])

<article class="relative overflow-hidden rounded-[24px] border border-border bg-surface px-6 py-8">
    <span class="absolute inset-y-0 left-0 w-1.5 bg-primary" aria-hidden="true"></span>
    <img src="{{ asset('assets/img/logo.webp') }}" alt="" class="h-7 w-auto">
    <p class="mt-8 text-[11px] font-bold uppercase tracking-[0.22em] text-primary">Séance faite</p>
    <h2 class="mt-2 font-display text-4xl font-bold uppercase leading-none tracking-wide text-text">
        {{ $log->workout?->title ?? 'Séance' }}
    </h2>
    @if ($log->member?->username)
        <p class="mt-3 text-sm text-muted">{{ '@'.$log->member->username }}</p>
    @endif
    <dl class="mt-8 flex flex-wrap gap-x-5 gap-y-2 text-sm text-text">
        <div>
            <dt class="sr-only">Durée</dt>
            <dd class="tabular-nums">{{ $log->durationMinutes() }} min</dd>
        </div>
        <div>
            <dt class="sr-only">Mode</dt>
            <dd>{{ $log->modeLabel() }}</dd>
        </div>
        <div>
            <dt class="sr-only">Points</dt>
            <dd class="tabular-nums text-primary">+{{ $log->points_awarded }} pts</dd>
        </div>
    </dl>
</article>
