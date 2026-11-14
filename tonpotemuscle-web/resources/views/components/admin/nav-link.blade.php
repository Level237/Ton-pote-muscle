@props([
    'href',
    'icon',
    'active' => false,
    'badge' => 0,
])

@php
    $classes = $active
        ? 'bg-primary/10 text-primary font-semibold'
        : 'text-muted hover:bg-white/[0.04] hover:text-text font-medium';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors duration-150 '.$classes]) }}>
    <span class="relative w-4 text-center">
        <i class="{{ $icon }} text-xs"></i>
        @if ((int) $badge > 0)
            <span class="absolute -right-2 -top-1.5 h-2 w-2 rounded-full bg-primary"></span>
        @endif
    </span>
    <span class="flex min-w-0 flex-1 items-center justify-between gap-2">
        <span>{{ $slot }}</span>
        <x-chat.badge :count="$badge" />
    </span>
</a>
