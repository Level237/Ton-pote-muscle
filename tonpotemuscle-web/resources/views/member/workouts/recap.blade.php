<x-layouts.focus :title="'C’est fait.'">
    <div class="mx-auto flex min-h-dvh w-full max-w-md flex-col px-4 py-6 sm:px-6">
        <p class="text-center text-[11px] font-bold uppercase tracking-[0.22em] text-primary">C’est fait</p>
        <h1 class="mt-2 text-center font-display text-4xl font-bold uppercase tracking-wide">Séance terminée</h1>
        <p class="mt-2 text-center text-sm text-muted">Envoie ça à tes potes, ou garde-le pour toi.</p>

        <div class="mt-8">
            <x-share.card-workout :log="$log" />
        </div>

        <div class="mt-8">
            <x-share.actions :url="$shareUrl" :title="$shareTitle" :text="$shareText" />
        </div>

        <a href="{{ route('dashboard') }}" class="mt-6 mb-4 inline-flex min-h-11 items-center justify-center text-sm font-medium text-muted hover:text-text">
            Retour à l’accueil
        </a>
    </div>
</x-layouts.focus>
