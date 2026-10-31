@php
    $inputClass = 'min-h-11 w-full rounded-lg border border-border bg-bg px-3 text-sm text-text outline-none focus:border-primary focus:ring-2 focus:ring-primary';
@endphp

<li class="program-slot grid gap-3 rounded-xl border border-border bg-bg p-4 md:grid-cols-[7rem_minmax(0,1fr)_auto] md:items-end">
    <x-admin.form.field :name="'slots.'.$index.'.day_number'" label="Jour">
        <input
            id="slots-{{ $index }}-day"
            name="slots[{{ $index }}][day_number]"
            type="number"
            min="1"
            max="366"
            value="{{ $slot['day_number'] ?? 1 }}"
            class="{{ $inputClass }}"
        >
    </x-admin.form.field>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-text" for="slots-{{ $index }}-workout">Séance</label>
        <select
            id="slots-{{ $index }}-workout"
            name="slots[{{ $index }}][workout_id]"
            class="{{ $inputClass }}"
        >
            <option value="">Choisir</option>
            @foreach ($workouts as $workout)
                <option value="{{ $workout->id }}" @selected((string) ($slot['workout_id'] ?? '') === (string) $workout->id)>
                    {{ $workout->title }}
                </option>
            @endforeach
        </select>
        @error('slots.'.$index.'.workout_id')
            <p class="mt-1.5 text-sm text-danger">{{ $message }}</p>
        @enderror
        @error('slots.'.$index.'.day_number')
            <p class="mt-1.5 text-sm text-danger">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex gap-1 md:justify-end">
        <button type="button" class="js-slot-remove min-h-11 px-2 text-sm text-muted hover:text-danger" aria-label="Retirer">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
    </div>
</li>
