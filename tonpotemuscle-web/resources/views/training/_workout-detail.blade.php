@php
    $workout = $item->workout;
@endphp

<h2 class="mt-2 font-display text-2xl font-bold uppercase leading-none tracking-wide text-text">{{ $item->label() }}</h2>
@if ($workout)
    <p class="mt-2 text-sm text-muted">
        @if ($workout->category)
            {{ $workout->category->title }} ·
        @endif
        {{ $workout->items->count() }} exercice{{ $workout->items->count() > 1 ? 's' : '' }}
        · {{ $workout->durationMinutes() }} min
    </p>

    <ul class="mt-5 space-y-4">
        @foreach ($workout->items as $row)
            <li>
                <p class="text-sm font-semibold text-text">{{ $row->exercise?->title }}</p>
                <p class="mt-0.5 text-sm text-muted">{{ $row->workLabel() }} · repos {{ $row->restLabel() }}</p>
                @if ($row->exercise?->demoUrl() && ! $row->exercise?->gifUrl())
                    <a href="{{ $row->exercise->demoUrl() }}" class="mt-1 inline-block text-sm text-primary hover:text-primary-hover" target="_blank" rel="noreferrer">
                        Voir la démo
                    </a>
                @endif
            </li>
        @endforeach
    </ul>
@endif
