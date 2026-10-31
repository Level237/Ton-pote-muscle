@props([
    'label',
    'value',
    'icon',
])

<article class="flex h-full items-center justify-between gap-3 rounded-xl border border-border bg-surface px-4 py-3 md:gap-4 md:px-5 md:py-4">
    <div>
        <p class="text-xs font-medium text-muted">{{ $label }}</p>
        <p class="mt-0.5 font-display text-2xl font-bold tabular-nums tracking-tight text-text md:mt-1 md:text-3xl">
            {{ number_format($value) }}
        </p>
    </div>
    <i class="{{ $icon }} text-sm text-muted/60" aria-hidden="true"></i>
</article>
