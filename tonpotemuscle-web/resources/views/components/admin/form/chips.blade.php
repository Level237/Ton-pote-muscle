@props([
    'name',
    'label',
    'options' => [],
    'selected' => [],
])

@php
    $selected = collect(old($name, $selected ?? []))->all();
@endphp

<fieldset>
    <legend class="mb-2 text-sm font-medium text-text">{{ $label }}</legend>
    <div class="flex flex-wrap gap-2">
        @foreach ($options as $value => $optionLabel)
            <label class="cursor-pointer">
                <input
                    type="checkbox"
                    name="{{ $name }}[]"
                    value="{{ $value }}"
                    class="peer sr-only"
                    @checked(in_array($value, $selected, true))
                >
                <span class="inline-flex min-h-11 items-center rounded-full border border-border px-3.5 text-sm text-muted transition-colors peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-primary">
                    {{ $optionLabel }}
                </span>
            </label>
        @endforeach
    </div>
    @error($name)
        <p class="mt-1.5 text-sm text-danger">{{ $message }}</p>
    @enderror
</fieldset>
