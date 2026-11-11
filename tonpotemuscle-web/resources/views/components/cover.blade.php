@props([
    'src' => null,
    'icon',
    'size' => 'thumb',
])

@php
    $frame = match ($size) {
        'hero' => 'h-36 w-full rounded-xl sm:h-48',
        'ticket' => 'h-full min-h-24 w-[5.5rem] rounded-none sm:w-28',
        default => 'h-12 w-12 rounded-lg',
    };
    $iconSize = $size === 'hero' ? 'text-2xl' : ($size === 'ticket' ? 'text-lg' : 'text-sm');
@endphp

@if ($src)
    <img src="{{ $src }}" alt="" {{ $attributes->merge(['class' => 'shrink-0 object-cover '.$frame]) }}>
@else
    <div {{ $attributes->merge(['class' => 'flex shrink-0 items-center justify-center bg-white/[0.04] text-primary '.$frame]) }} aria-hidden="true">
        <i class="{{ $icon }} {{ $iconSize }}"></i>
    </div>
@endif
