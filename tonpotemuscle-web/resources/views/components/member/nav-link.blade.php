@props([
    'href',
    'icon',
    'active' => false,
])

@php
    $classes = $active
        ? 'bg-primary/10 text-primary font-semibold'
        : 'text-muted hover:bg-white/[0.04] hover:text-text font-medium';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors duration-150 '.$classes]) }}>
    <i class="{{ $icon }} w-4 text-center text-xs"></i>
    <span>{{ $slot }}</span>
</a>
