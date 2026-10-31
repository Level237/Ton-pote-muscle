<x-layouts.guest title="Connexion administration" heading="Connexion" subtitle="Administration">
    <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
        @csrf

        <x-auth.field
            name="email"
            label="Adresse e-mail"
            type="email"
            autocomplete="username"
            placeholder="admin@exemple.com"
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
</x-layouts.guest>
