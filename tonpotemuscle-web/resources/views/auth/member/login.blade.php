<x-layouts.guest title="Connexion" heading="Connexion">
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <x-auth.field
            name="email"
            label="Adresse e-mail"
            type="email"
            autocomplete="username"
            placeholder="nom@exemple.com"
            required
            autofocus
        />

        <x-auth.field
            name="password"
            label="Mot de passe"
            type="password"
            autocomplete="current-password"
            required
        />

        <div class="pt-1">
            <x-auth.submit>Se connecter</x-auth.submit>
        </div>
    </form>

    <div class="mt-6 border-t border-border pt-5 text-center text-sm text-muted">
        Pas encore de compte ?
        <a href="{{ route('register') }}" class="ml-1 font-semibold text-primary hover:text-primary-hover">
            Inscription
        </a>
    </div>
</x-layouts.guest>
