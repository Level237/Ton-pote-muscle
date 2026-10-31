@props([
    'title',
    'href',
    'emptyText',
    'isEmpty' => false,
])

<section class="flex flex-col rounded-xl border border-border bg-surface">
    <div class="flex items-center justify-between gap-3 border-b border-border px-5 py-4">
        <h2 class="text-sm font-semibold text-text">{{ $title }}</h2>

        <a
            href="{{ $href }}"
            class="text-xs font-medium text-muted transition-colors hover:text-primary"
        >
            Voir tout
        </a>
    </div>

    @if ($isEmpty)
        <p class="px-5 py-8 text-sm text-muted">{{ $emptyText }}</p>
    @else
        {{ $slot }}
    @endif
</section>
