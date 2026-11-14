@props([
    'href',
    'icon',
    'active' => false,
    'badge' => 0,
])

@php
    $classes = $active ? 'text-primary' : 'text-muted';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'flex min-w-0 flex-1 flex-col items-center gap-1 py-2 '.$classes]) }}>
    <span class="relative">
        <i class="{{ $icon }} text-base"></i>
        @if ((int) $badge > 0)
            <span class="absolute -right-2 -top-1 h-2 w-2 rounded-full bg-primary"></span>
        @endif
    </span>
    <span class="truncate text-[10px] font-medium">{{ $slot }}</span>
</a>
