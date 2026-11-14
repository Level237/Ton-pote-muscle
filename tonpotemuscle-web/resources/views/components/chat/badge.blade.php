@props([
    'count' => 0,
])

@if ((int) $count > 0)
    <span
        {{ $attributes->class('inline-flex min-w-4 items-center justify-center rounded-full bg-primary px-1 text-[10px] font-bold leading-4 text-bg') }}
        aria-label="{{ (int) $count }} message{{ (int) $count > 1 ? 's' : '' }} non lu{{ (int) $count > 1 ? 's' : '' }}"
    >
        {{ (int) $count > 9 ? '9+' : (int) $count }}
    </span>
@endif
