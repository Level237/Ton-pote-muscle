@props([
    'name',
    'label',
    'hint' => null,
])

<div>
    <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-text">
        {{ $label }}
    </label>
    {{ $slot }}
    @if ($hint)
        <p class="mt-1.5 text-xs text-muted">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1.5 text-sm text-danger">{{ $message }}</p>
    @enderror
</div>
