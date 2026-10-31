@props([
    'label',
    'value',
    'icon',
])

<article class="flex items-center justify-between gap-4 rounded-xl border border-border bg-surface px-5 py-4">
    <div>
        <p class="text-xs font-medium text-muted">{{ $label }}</p>
        <p class="mt-1 font-display text-3xl font-bold tabular-nums tracking-tight text-text">
            {{ number_format($value) }}
        </p>
    </div>
    <i class="{{ $icon }} text-sm text-muted/60"></i>
</article>
