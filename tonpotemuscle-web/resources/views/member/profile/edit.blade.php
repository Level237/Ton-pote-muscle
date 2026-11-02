@php
    $inputClass = 'min-h-11 w-full rounded-lg border border-border bg-bg px-3 text-sm text-text outline-none placeholder:text-muted/50 focus:border-primary focus:ring-2 focus:ring-primary';
@endphp

<x-layouts.member title="Profil">
    <div class="space-y-6">
        <h1 class="font-display text-2xl font-bold uppercase tracking-wide">Profil</h1>

        <form method="POST" action="{{ route('member.profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <x-admin.form.field name="name" label="Nom">
                <input id="name" name="name" type="text" value="{{ old('name', $member->name) }}" required class="{{ $inputClass }}">
            </x-admin.form.field>

            <x-admin.form.field name="username" label="Pseudo">
                <input id="username" name="username" type="text" value="{{ old('username', $member->username) }}" required class="{{ $inputClass }}">
            </x-admin.form.field>

            <p class="text-sm text-muted">{{ $member->email }}</p>

            <x-admin.form.radios name="gender" label="Sexe" :options="$genders" :selected="$member->gender" />

            <div class="grid grid-cols-2 gap-3">
                <x-admin.form.field name="height_cm" label="Taille (cm)">
                    <input id="height_cm" name="height_cm" type="number" min="100" max="250" value="{{ old('height_cm', $member->height_cm) }}" class="{{ $inputClass }}">
                </x-admin.form.field>

                <x-admin.form.field name="weight_kg" label="Poids (kg)">
                    <input id="weight_kg" name="weight_kg" type="number" step="0.1" min="30" max="300" value="{{ old('weight_kg', $member->weight_kg) }}" class="{{ $inputClass }}">
                </x-admin.form.field>
            </div>

            <x-admin.form.radios name="main_goal" label="Objectif" :options="$goals" :selected="$member->main_goal" />
            <x-admin.form.radios name="fitness_level" label="Niveau d’entraînement" :options="$fitnessLevels" :selected="$member->fitness_level" />
            <x-admin.form.radios name="activity_level" label="Niveau d’activité" :options="$activityLevels" :selected="$member->activity_level" />
            <x-admin.form.chips name="motivations" label="Motivations" :options="$motivations" :selected="$member->motivations ?? []" />

            <button
                type="submit"
                class="inline-flex min-h-11 w-full items-center justify-center rounded-lg bg-primary px-4 font-display text-xs font-bold uppercase tracking-wider text-bg transition-colors hover:bg-primary-hover"
            >
                Enregistrer
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="lg:hidden">
            @csrf
            <button type="submit" class="w-full py-3 text-sm font-medium text-muted hover:text-primary">
                Déconnexion
            </button>
        </form>
    </div>
</x-layouts.member>
