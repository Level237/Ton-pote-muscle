<x-layouts.guest title="Ton Pote Musclé" heading="Bienvenue">
    <div class="space-y-3">
        <a
            href="{{ route('login') }}"
            class="inline-flex min-h-11 w-full items-center justify-center rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-bg transition-colors hover:bg-primary-hover"
        >
            Se connecter
        </a>

        <a
            href="{{ route('register') }}"
            class="inline-flex min-h-11 w-full items-center justify-center rounded-lg border border-border bg-bg px-4 py-2.5 text-sm font-semibold text-text transition-colors hover:border-primary/50 hover:text-primary"
        >
            Inscription
        </a>
    </div>
</x-layouts.guest>
