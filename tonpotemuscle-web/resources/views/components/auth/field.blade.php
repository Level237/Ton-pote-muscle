@props([
    'name',
    'label',
    'type' => 'text',
    'autocomplete' => null,
])

@php
    $isPassword = $type === 'password';
    $hasError = $errors->has($name);
@endphp

<div>
    <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-text">
        {{ $label }}
    </label>
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ $isPassword ? '' : old($name) }}"
        @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        {{ $attributes->merge([
            'class' => 'min-h-11 w-full rounded-lg border bg-bg px-3.5 text-sm text-text outline-none transition-colors placeholder:text-muted/50 focus:border-primary focus:ring-2 focus:ring-primary/20 '.($hasError ? 'border-danger' : 'border-border'),
        ]) }}
    >
    @error($name)
        <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
    @enderror
</div>
