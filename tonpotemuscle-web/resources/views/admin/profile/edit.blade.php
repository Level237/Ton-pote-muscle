@php
    $inputClass = 'min-h-11 w-full max-w-md rounded-lg border border-border bg-bg px-3 text-sm text-text outline-none placeholder:text-muted/50 focus:border-primary focus:ring-2 focus:ring-primary';
@endphp

<x-layouts.admin title="Profil">
    <div class="max-w-md space-y-6">
        <h1 class="font-display text-2xl font-bold uppercase tracking-wide">Profil</h1>

        <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <x-admin.form.field name="name" label="Nom">
                <input id="name" name="name" type="text" value="{{ old('name', $admin->name) }}" required class="{{ $inputClass }}">
            </x-admin.form.field>

            <x-admin.form.field name="username" label="Pseudo">
                <input id="username" name="username" type="text" value="{{ old('username', $admin->username) }}" required class="{{ $inputClass }}">
            </x-admin.form.field>

            <x-admin.form.field name="email" label="Adresse e-mail">
                <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email', $admin->email) }}" required class="{{ $inputClass }}">
            </x-admin.form.field>

            <x-admin.form.field name="phone" label="Numéro de téléphone">
                <input id="phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone', $admin->phone) }}" class="{{ $inputClass }}">
            </x-admin.form.field>

            <div class="border-t border-border pt-5 space-y-5">
                <p class="text-sm text-muted">Laisse vide si tu ne changes pas le mot de passe.</p>

                <x-admin.form.field name="current_password" label="Mot de passe actuel">
                    <input id="current_password" name="current_password" type="password" autocomplete="current-password" class="{{ $inputClass }}">
                </x-admin.form.field>

                <x-admin.form.field name="password" label="Nouveau mot de passe">
                    <input id="password" name="password" type="password" autocomplete="new-password" class="{{ $inputClass }}">
                </x-admin.form.field>

                <x-admin.form.field name="password_confirmation" label="Confirmation">
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="{{ $inputClass }}">
                </x-admin.form.field>
            </div>

            <button
                type="submit"
                class="inline-flex min-h-11 items-center justify-center rounded-lg bg-primary px-4 font-display text-xs font-bold uppercase tracking-wider text-bg transition-colors hover:bg-primary-hover"
            >
                Enregistrer
            </button>
        </form>
    </div>
</x-layouts.admin>
