<x-layouts.guest title="Connexion">
    <div class="w-full max-w-[20.5rem]">
        <p class="mb-10 text-center font-display text-xs font-semibold uppercase tracking-[0.35em] text-primary">Connexion</p>

        <form method="POST" action="{{ route('admin.login') }}" class="space-y-8">
            @csrf

            <x-auth.field name="email" label="E-mail" type="email" autocomplete="username" required autofocus />

            <x-auth.field name="password" label="Mot de passe" type="password" autocomplete="current-password" required />

            <x-auth.submit>Se connecter</x-auth.submit>
        </form>
    </div>
</x-layouts.guest>
