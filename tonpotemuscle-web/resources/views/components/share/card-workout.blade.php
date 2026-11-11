@props([
    'log',
])

<article class="overflow-hidden rounded-2xl border border-border bg-surface">
    <div class="flex items-stretch">
        <x-cover
            :src="$log->workout?->thumbnailUrl()"
            icon="fa-solid fa-stopwatch"
            size="ticket"
        />
        <div class="flex min-w-0 flex-1 flex-col justify-center gap-1 px-3.5 py-3">
            <div class="flex items-center justify-between gap-2">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">Séance faite</p>
                <img src="{{ asset('assets/img/logo.webp') }}" alt="" class="h-4 w-auto opacity-80">
            </div>
            <h2 class="font-display text-xl font-bold uppercase leading-tight tracking-wide text-text sm:text-2xl">
                {{ $log->workout?->title ?? 'Séance' }}
            </h2>
            @if ($log->member?->username)
                <p class="text-xs text-muted">{{ '@'.$log->member->username }}</p>
            @endif
        </div>
    </div>
    <dl class="grid grid-cols-3 border-t border-border">
        <div class="px-3 py-2.5 text-center">
            <dt class="text-[10px] font-medium uppercase tracking-wider text-muted">Durée</dt>
            <dd class="mt-0.5 text-sm font-semibold tabular-nums text-text">{{ $log->durationMinutes() }} min</dd>
        </div>
        <div class="border-x border-border px-3 py-2.5 text-center">
            <dt class="text-[10px] font-medium uppercase tracking-wider text-muted">Mode</dt>
            <dd class="mt-0.5 text-sm font-semibold text-text">{{ $log->modeLabel() }}</dd>
        </div>
        <div class="px-3 py-2.5 text-center">
            <dt class="text-[10px] font-medium uppercase tracking-wider text-muted">Points</dt>
            <dd class="mt-0.5 text-sm font-semibold tabular-nums text-primary">+{{ $log->points_awarded }} pts</dd>
        </div>
    </dl>
</article>
