@php
    use App\Enums\ProgramStatus;
    use App\Support\WorkoutCatalog;

    $inputClass = 'min-h-11 w-full rounded-lg border border-border bg-bg px-3 text-sm text-text outline-none placeholder:text-muted/50 focus:border-primary focus:ring-2 focus:ring-primary';
    $startsOn = old('starts_on', optional($program->starts_on)->format('Y-m-d'));
    $endsOn = old('ends_on', optional($program->ends_on)->format('Y-m-d'));
@endphp

<div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_20rem]">
    <div class="space-y-6">
        <x-admin.form.field name="title" label="Titre">
            <input
                id="title"
                name="title"
                type="text"
                value="{{ old('title', $program->title) }}"
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
            >{{ old('description', $program->description) }}</textarea>
        </x-admin.form.field>

        <x-admin.form.field name="objective" label="Objectif">
            <input
                id="objective"
                name="objective"
                type="text"
                value="{{ old('objective', $program->objective) }}"
                maxlength="120"
                class="{{ $inputClass }}"
            >
        </x-admin.form.field>

        <div class="grid gap-6 sm:grid-cols-2">
            <x-admin.form.field name="starts_on" label="Début">
                <input id="starts_on" name="starts_on" type="date" value="{{ $startsOn }}" required class="{{ $inputClass }}">
            </x-admin.form.field>
            <x-admin.form.field name="ends_on" label="Fin">
                <input id="ends_on" name="ends_on" type="date" value="{{ $endsOn }}" required class="{{ $inputClass }}">
            </x-admin.form.field>
        </div>

        <x-admin.form.radios
            name="level"
            label="Niveau"
            :options="WorkoutCatalog::DIFFICULTIES"
            :selected="$program->level"
        />

        <x-admin.form.radios
            name="status"
            label="Statut"
            :options="collect(ProgramStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()])->all()"
            :selected="old('status', $program->status?->value ?? ProgramStatus::Draft->value)"
        />

        <div>
            <div class="mb-2 flex items-center justify-between gap-3">
                <h2 class="text-sm font-medium text-text">Calendrier</h2>
                <button
                    type="button"
                    id="add-program-slot"
                    class="text-sm font-medium text-primary hover:text-primary-hover"
                    @disabled($workouts->isEmpty())
                >
                    Ajouter
                </button>
            </div>

            @if ($workouts->isEmpty())
                <p class="rounded-xl border border-border bg-bg px-4 py-3 text-sm text-muted">
                    <a href="{{ route('admin.workouts.create') }}" class="text-primary hover:text-primary-hover">Crée une séance</a>
                    avant de composer un programme.
                </p>
            @else
                <ul id="program-slots" class="space-y-3">
                    @foreach ($slots as $index => $slot)
                        @include('admin.programs._slot', [
                            'index' => $index,
                            'slot' => $slot,
                            'workouts' => $workouts,
                            'startsOn' => $startsOn,
                        ])
                    @endforeach
                </ul>
                @error('slots')
                    <p class="mt-1.5 text-sm text-danger">{{ $message }}</p>
                @enderror

                <template id="program-slot-template">
                    @include('admin.programs._slot', [
                        'index' => '__INDEX__',
                        'slot' => [
                            'day_number' => '',
                            'kind' => 'rest',
                            'workout_id' => '',
                        ],
                        'workouts' => $workouts,
                        'startsOn' => $startsOn,
                    ])
                </template>
            @endif
        </div>
    </div>

    <div class="space-y-6">
        @if ($program->exists)
            <x-admin.form.field name="public_url" label="URL publique">
                <input
                    id="public_url"
                    type="text"
                    readonly
                    value="{{ $program->publicUrl() }}"
                    class="{{ $inputClass }}"
                >
                <p class="mt-1.5 text-xs text-muted">
                    @if ($program->isPublic())
                        À envoyer aux membres.
                    @else
                        Visible une fois le programme ouvert.
                    @endif
                </p>
            </x-admin.form.field>
        @endif

        <x-admin.form.field name="image" label="Visuel">
            @if ($program->imageUrl())
                <img src="{{ $program->imageUrl() }}" alt="" class="mb-3 h-40 w-full rounded-xl object-cover">
                <label class="mb-3 flex items-center gap-2 text-sm text-muted">
                    <input type="checkbox" name="remove_image" value="1" class="rounded border-border bg-bg text-primary focus:ring-primary">
                    Retirer le visuel
                </label>
            @endif
            <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-muted file:mr-3 file:rounded-lg file:border-0 file:bg-surface file:px-3 file:py-2 file:text-sm file:font-medium file:text-text">
        </x-admin.form.field>
    </div>
</div>

@if ($workouts->isNotEmpty())
    <script>
        (() => {
            const list = document.getElementById('program-slots');
            const template = document.getElementById('program-slot-template');
            const addButton = document.getElementById('add-program-slot');
            if (!list || !template || !addButton) return;

            let nextIndex = list.querySelectorAll('.program-slot').length;

            const syncKind = (slot) => {
                const kind = slot.querySelector('.js-slot-kind')?.value;
                const wrap = slot.querySelector('.js-slot-workout-wrap');
                const select = slot.querySelector('.js-slot-workout');
                const isRest = kind === 'rest';
                wrap?.classList.toggle('hidden', isRest);
                if (select) {
                    select.disabled = isRest;
                    if (isRest) select.value = '';
                }
            };

            addButton.addEventListener('click', () => {
                const html = template.innerHTML.replaceAll('__INDEX__', String(nextIndex++));
                list.insertAdjacentHTML('beforeend', html);
                const slot = list.lastElementChild;
                if (slot) syncKind(slot);
            });

            list.addEventListener('change', (event) => {
                if (!event.target.classList.contains('js-slot-kind')) return;
                const slot = event.target.closest('.program-slot');
                if (slot) syncKind(slot);
            });

            list.addEventListener('click', (event) => {
                const slot = event.target.closest('.program-slot');
                if (!slot || !event.target.closest('.js-slot-remove')) return;
                if (list.querySelectorAll('.program-slot').length > 1) {
                    slot.remove();
                }
            });

            list.querySelectorAll('.program-slot').forEach(syncKind);
        })();
    </script>
@endif
