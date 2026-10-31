@props([
    'name',
    'label',
    'type' => 'text',
    'autocomplete' => null,
])

@php
    $isPassword = $type === 'password';
@endphp

<div>
    <label for="{{ $name }}" class="mb-2 block text-[11px] font-medium uppercase tracking-[0.2em] text-muted">
        {{ $label }}
    </label>
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ $isPassword ? '' : old($name) }}"
        @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        {{ $attributes->merge([
            'class' => 'w-full border-0 border-b border-white/15 bg-transparent px-0 py-2.5 text-[15px] text-text caret-primary outline-none transition-[border-color] duration-150 placeholder:text-muted/40 focus:border-primary',
        ]) }}
    >
    @error($name)
        <p class="mt-2 text-sm text-danger">{{ $message }}</p>
    @enderror
</div>
