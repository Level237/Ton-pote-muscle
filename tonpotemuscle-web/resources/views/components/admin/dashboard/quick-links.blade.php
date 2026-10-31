@props([
    'links' => [],
])

<section aria-labelledby="quick-links-heading">
    <h2 id="quick-links-heading" class="text-xs font-semibold uppercase tracking-wider text-muted">
        Liens rapides
    </h2>

    <div class="mt-3 grid divide-y divide-border overflow-hidden rounded-xl border border-border bg-surface sm:grid-cols-2 sm:divide-y-0 lg:grid-cols-5 lg:divide-x">
        @foreach ($links as $link)
            <a
                href="{{ $link['href'] }}"
                class="group flex flex-col justify-between gap-3 px-5 py-4 transition-colors duration-150 hover:bg-white/[0.03]"
            >
                <span class="text-sm font-semibold text-text transition-colors group-hover:text-primary">
                    {{ $link['label'] }}
                </span>

                @if (! empty($link['description']))
                    <span class="text-xs leading-relaxed text-muted">
                        {{ $link['description'] }}
                    </span>
                @endif
            </a>
        @endforeach
    </div>
</section>
