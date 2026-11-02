@php
    use App\Support\ExerciseCatalog;

    $inputClass = 'min-h-11 w-full rounded-lg border border-border bg-bg px-3 text-sm text-text outline-none transition-colors placeholder:text-muted/50 focus:border-primary focus:ring-2 focus:ring-primary';
@endphp

<div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_20rem]">
    <div class="space-y-6">
        <x-admin.form.field name="title" label="Titre">
            <input
                id="title"
                name="title"
                type="text"
                value="{{ old('title', $exercise->title) }}"
                required
                maxlength="120"
                class="{{ $inputClass }}"
            >
        </x-admin.form.field>

        <x-admin.form.field name="description" label="Description">
            <textarea
                id="description"
                name="description"
                rows="4"
                maxlength="2000"
                class="{{ $inputClass }} py-2.5"
            >{{ old('description', $exercise->description) }}</textarea>
        </x-admin.form.field>

        <x-admin.form.chips
            name="body_parts"
            label="Zones"
            :options="ExerciseCatalog::BODY_PARTS"
            :selected="$exercise->body_parts ?? []"
        />

        <x-admin.form.chips
            name="equipments"
            label="Matériel"
            :options="ExerciseCatalog::EQUIPMENTS"
            :selected="$exercise->equipments ?? []"
        />

        <x-admin.form.field name="target_muscles" label="Muscles ciblés" hint="Séparés par des virgules.">
            <input
                id="target_muscles"
                name="target_muscles"
                type="text"
                value="{{ old('target_muscles', implode(', ', $exercise->target_muscles ?? [])) }}"
                class="{{ $inputClass }}"
            >
        </x-admin.form.field>

        <x-admin.form.field name="secondary_muscles" label="Muscles secondaires" hint="Séparés par des virgules.">
            <input
                id="secondary_muscles"
                name="secondary_muscles"
                type="text"
                value="{{ old('secondary_muscles', implode(', ', $exercise->secondary_muscles ?? [])) }}"
                class="{{ $inputClass }}"
            >
        </x-admin.form.field>

        <x-admin.form.field name="instructions" label="Consignes" hint="Une étape par ligne.">
            <textarea
                id="instructions"
                name="instructions"
                rows="6"
                maxlength="5000"
                class="{{ $inputClass }} py-2.5"
            >{{ old('instructions', implode("\n", $exercise->instructions ?? [])) }}</textarea>
        </x-admin.form.field>
    </div>

    <div class="space-y-6">
        <x-admin.form.field name="image" label="Image">
            @if ($exercise->imageUrl())
                <img src="{{ $exercise->imageUrl() }}" alt="" class="mb-3 h-40 w-full rounded-xl object-cover">
                <label class="mb-3 flex items-center gap-2 text-sm text-muted">
                    <input type="checkbox" name="remove_image" value="1" class="rounded border-border bg-bg text-primary focus:ring-primary">
                    Retirer l’image
                </label>
            @endif
            <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-muted file:mr-3 file:rounded-lg file:border-0 file:bg-surface file:px-3 file:py-2 file:text-sm file:font-medium file:text-text">
        </x-admin.form.field>

        <x-admin.form.field name="gif" label="GIF">
            @if ($exercise->gifUrl())
                <img src="{{ $exercise->gifUrl() }}" alt="" class="mb-3 h-40 w-full rounded-xl object-cover">
                <label class="mb-3 flex items-center gap-2 text-sm text-muted">
                    <input type="checkbox" name="remove_gif" value="1" class="rounded border-border bg-bg text-primary focus:ring-primary">
                    Retirer le GIF
                </label>
            @endif
            <input id="gif" name="gif" type="file" accept="image/gif,image/webp" class="block w-full text-sm text-muted file:mr-3 file:rounded-lg file:border-0 file:bg-surface file:px-3 file:py-2 file:text-sm file:font-medium file:text-text">
        </x-admin.form.field>

        <x-admin.form.field name="video_url" label="URL vidéo" hint="Lien YouTube, Vimeo ou autre. Sans GIF, le membre ouvre cette démo.">
            <input
                id="video_url"
                name="video_url"
                type="url"
                value="{{ old('video_url', $exercise->video_url) }}"
                placeholder="https://"
                class="{{ $inputClass }}"
            >
        </x-admin.form.field>
    </div>
</div>
