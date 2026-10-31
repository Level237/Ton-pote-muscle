<button
    type="submit"
    {{ $attributes->merge([
        'class' => 'inline-flex min-h-11 w-full items-center justify-center rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-bg transition-colors hover:bg-primary-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary active:translate-y-px',
    ]) }}
>
    {{ $slot }}
</button>
