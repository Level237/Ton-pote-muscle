<x-layouts.guest title="Connexion">
    <div class="w-full max-w-[20.5rem]">
        <form method="POST" action="{{ route('login') }}" class="space-y-8">
            @csrf

            <x-auth.field name="email" label="E-mail" type="email" autocomplete="username" required autofocus />

            <x-auth.field name="password" label="Mot de passe" type="password" autocomplete="current-password" required />

            <x-auth.submit>Se connecter</x-auth.submit>
        </form>

        <p class="mt-8 text-center text-sm text-muted">
            Pas de compte ?
            <a href="{{ route('register') }}" class="font-semibold text-primary hover:text-primary-hover">Inscription</a>
        </p>
    </div>
</x-layouts.guest>
