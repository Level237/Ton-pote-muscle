<x-layouts.guest title="Inscription">
    <div class="w-full max-w-[20.5rem]">
        <form method="POST" action="{{ route('register') }}" class="space-y-8">
            @csrf

            <x-auth.field name="name" label="Nom" autocomplete="name" required autofocus />

            <x-auth.field name="username" label="Pseudo" autocomplete="username" required />

            <x-auth.field name="email" label="E-mail" type="email" autocomplete="email" required />

            <x-auth.field name="password" label="Mot de passe" type="password" autocomplete="new-password" required />

            <x-auth.field name="password_confirmation" label="Confirmation" type="password" autocomplete="new-password" required />

            <x-auth.submit>S’inscrire</x-auth.submit>
        </form>

        <p class="mt-8 text-center text-sm text-muted">
            Déjà un compte ?
            <a href="{{ route('login') }}" class="font-semibold text-primary hover:text-primary-hover">Connexion</a>
        </p>
    </div>
</x-layouts.guest>
