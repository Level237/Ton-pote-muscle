<x-layouts.guest title="Inscription" heading="Créer un compte">
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div class="grid gap-4 sm:grid-cols-2">
            <x-auth.field
                name="name"
                label="Nom"
                autocomplete="name"
                placeholder="Jean Dupont"
                required
                autofocus
            />

            <x-auth.field
                name="username"
                label="Pseudo"
                autocomplete="username"
                placeholder="jeandupont"
                required
            />
        </div>

        <x-auth.field
            name="email"
            label="Adresse e-mail"
            type="email"
            autocomplete="email"
            placeholder="nom@exemple.com"
            required
        />

        <div class="grid gap-4 sm:grid-cols-2">
            <x-auth.field
                name="password"
                label="Mot de passe"
                type="password"
                autocomplete="new-password"
                required
            />

            <x-auth.field
                name="password_confirmation"
                label="Confirmation"
                type="password"
                autocomplete="new-password"
                required
            />
        </div>

        <div class="pt-1">
            <x-auth.submit>S’inscrire</x-auth.submit>
        </div>
    </form>

    <div class="mt-6 border-t border-border pt-5 text-center text-sm text-muted">
        Déjà un compte ?
        <a href="{{ route('login') }}" class="ml-1 font-semibold text-primary hover:text-primary-hover">
            Connexion
        </a>
    </div>
</x-layouts.guest>
