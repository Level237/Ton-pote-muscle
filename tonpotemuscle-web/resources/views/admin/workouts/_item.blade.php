@php
    $inputClass = 'min-h-11 w-full rounded-lg border border-border bg-bg px-3 text-sm text-text outline-none focus:border-primary focus:ring-2 focus:ring-primary';
@endphp

<li class="workout-item space-y-3 rounded-xl border border-border bg-bg p-4">
    <div class="flex items-start gap-3">
        <div class="min-w-0 flex-1">
            <label class="mb-1.5 block text-sm font-medium text-text" for="items-{{ $index }}-exercise">Exercice</label>
            <select
                id="items-{{ $index }}-exercise"
                name="items[{{ $index }}][exercise_id]"
                class="{{ $inputClass }}"
            >
                <option value="">Choisir</option>
                @foreach ($exercises as $exercise)
                    <option value="{{ $exercise->id }}" @selected((string) ($item['exercise_id'] ?? '') === (string) $exercise->id)>
                        {{ $exercise->title }}
                    </option>
                @endforeach
            </select>
            @error('items.'.$index.'.exercise_id')
                <p class="mt-1.5 text-sm text-danger">{{ $message }}</p>
            @enderror
            @error('items.'.$index.'.reps')
                <p class="mt-1.5 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex shrink-0 gap-1 pt-7">
            <button type="button" class="js-item-up min-h-11 px-2 text-sm text-muted hover:text-text" aria-label="Monter">
                <i class="fa-solid fa-arrow-up" aria-hidden="true"></i>
            </button>
            <button type="button" class="js-item-down min-h-11 px-2 text-sm text-muted hover:text-text" aria-label="Descendre">
                <i class="fa-solid fa-arrow-down" aria-hidden="true"></i>
            </button>
            <button type="button" class="js-item-remove min-h-11 px-2 text-sm text-muted hover:text-danger" aria-label="Retirer">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <x-admin.form.field :name="'items.'.$index.'.sets'" label="Séries">
            <input
                id="items-{{ $index }}-sets"
                name="items[{{ $index }}][sets]"
                type="number"
                min="1"
                max="20"
                value="{{ $item['sets'] ?? 1 }}"
                required
                class="{{ $inputClass }}"
            >
        </x-admin.form.field>

        <x-admin.form.field :name="'items.'.$index.'.reps'" label="Reps">
            <input
                id="items-{{ $index }}-reps"
                name="items[{{ $index }}][reps]"
                type="number"
                min="1"
                max="999"
                value="{{ $item['reps'] ?? '' }}"
                class="{{ $inputClass }}"
            >
        </x-admin.form.field>

        <x-admin.form.field :name="'items.'.$index.'.duration_seconds'" label="Durée (s)">
            <input
                id="items-{{ $index }}-duration"
                name="items[{{ $index }}][duration_seconds]"
                type="number"
                min="1"
                max="3600"
                value="{{ $item['duration_seconds'] ?? '' }}"
                class="{{ $inputClass }}"
            >
        </x-admin.form.field>

        <x-admin.form.field :name="'items.'.$index.'.rest_seconds'" label="Repos (s)">
            <input
                id="items-{{ $index }}-rest"
                name="items[{{ $index }}][rest_seconds]"
                type="number"
                min="0"
                max="600"
                value="{{ $item['rest_seconds'] ?? 30 }}"
                required
                class="{{ $inputClass }}"
            >
        </x-admin.form.field>
    </div>
</li>
