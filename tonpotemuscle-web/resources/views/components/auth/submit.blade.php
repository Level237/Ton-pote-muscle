<button
    type="submit"
    {{ $attributes->merge([
        'class' => 'auth-cta mt-2 w-full bg-primary py-3.5 font-display text-[15px] font-bold uppercase tracking-[0.14em] text-bg transition-colors duration-150 hover:bg-primary-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary',
    ]) }}
>
    {{ $slot }}
</button>
