<x-layouts.guest title="Ton Pote Musclé">
    <div class="w-full max-w-[20.5rem]">
        <a href="{{ route('login') }}">
            <span class="auth-cta block w-full bg-primary py-3.5 text-center font-display text-[15px] font-bold uppercase tracking-[0.14em] text-bg transition-colors duration-150 hover:bg-primary-hover">
                Se connecter
            </span>
        </a>
        <p class="mt-8 text-center text-sm text-muted">
            Pas de compte ?
            <a href="{{ route('register') }}" class="font-semibold text-primary hover:text-primary-hover">Inscription</a>
        </p>
    </div>
</x-layouts.guest>
