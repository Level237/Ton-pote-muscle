@props([
    'href' => null,
    'icon',
    'active' => false,
    'tag' => 'a',
])

@php
    $classes = $active
        ? 'text-primary'
        : 'text-muted';
@endphp

@if ($tag === 'label')
    <label {{ $attributes->merge(['class' => 'flex min-w-0 flex-1 flex-col items-center gap-1 py-2 '.$classes]) }}>
        <i class="{{ $icon }} text-base"></i>
        <span class="truncate text-[10px] font-medium">{{ $slot }}</span>
    </label>
@else
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'flex min-w-0 flex-1 flex-col items-center gap-1 py-2 '.$classes]) }}>
        <i class="{{ $icon }} text-base"></i>
        <span class="truncate text-[10px] font-medium">{{ $slot }}</span>
    </a>
@endif
