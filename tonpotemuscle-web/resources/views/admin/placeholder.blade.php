<x-layouts.admin :title="$title">
    <header>
        <h1 class="font-display text-2xl font-bold uppercase tracking-wide">{{ $title }}</h1>
        @if (! empty($description))
            <p class="mt-1 text-sm text-muted">{{ $description }}</p>
        @endif
    </header>
</x-layouts.admin>
