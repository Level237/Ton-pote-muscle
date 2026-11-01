@php
    use App\Support\WorkoutCatalog;

    $inputClass = 'min-h-11 w-full rounded-lg border border-border bg-bg px-3 text-sm text-text outline-none placeholder:text-muted/50 focus:border-primary focus:ring-2 focus:ring-primary';
@endphp

<div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_20rem]">
    <div class="space-y-6">
        <x-admin.form.field name="title" label="Titre">
            <input
                id="title"
                name="title"
                type="text"
                value="{{ old('title', $workout->title) }}"
                required
                maxlength="120"
                class="{{ $inputClass }}"
            >
        </x-admin.form.field>

        <x-admin.form.field name="category_id" label="Catégorie">
            <select id="category_id" name="category_id" class="{{ $inputClass }}">
                <option value="">Sans catégorie</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('category_id', $workout->category_id) === (string) $category->id)>
                        {{ $category->title }}
                    </option>
                @endforeach
            </select>
        </x-admin.form.field>

        <x-admin.form.radios
            name="difficulty"
            label="Niveau"
            :options="WorkoutCatalog::DIFFICULTIES"
            :selected="$workout->difficulty"
        />

        <x-admin.form.field name="notes" label="Note">
            <textarea
                id="notes"
                name="notes"
                rows="4"
                maxlength="2000"
                class="{{ $inputClass }} py-2.5"
            >{{ old('notes', $workout->notes) }}</textarea>
        </x-admin.form.field>

        <div>
            <div class="mb-2 flex items-center justify-between gap-3">
                <h2 class="text-sm font-medium text-text">Exercices</h2>
                <button
                    type="button"
                    id="add-workout-item"
                    class="text-sm font-medium text-primary hover:text-primary-hover"
                    @disabled($exercises->isEmpty())
                >
                    Ajouter
                </button>
            </div>

            @if ($exercises->isEmpty())
                <p class="rounded-xl border border-border bg-bg px-4 py-3 text-sm text-muted">
                    <a href="{{ route('admin.exercises.create') }}" class="text-primary hover:text-primary-hover">Crée un exercice</a>
                    avant de composer une séance.
                </p>
            @else
                <ul id="workout-items" class="space-y-3">
                    @foreach ($items as $index => $item)
                        @include('admin.workouts._item', [
                            'index' => $index,
                            'item' => $item,
                            'exercises' => $exercises,
                        ])
                    @endforeach
                </ul>
                @error('items')
                    <p class="mt-1.5 text-sm text-danger">{{ $message }}</p>
                @enderror

                <template id="workout-item-template">
                    @include('admin.workouts._item', [
                        'index' => '__INDEX__',
                        'item' => [
                            'exercise_id' => '',
                            'sets' => 1,
                            'reps' => '',
                            'duration_seconds' => '',
                            'rest_seconds' => 30,
                        ],
                        'exercises' => $exercises,
                    ])
                </template>
            @endif
        </div>
    </div>

    <div class="space-y-6">
        <x-admin.form.field name="thumbnail" label="Visuel">
            @if ($workout->thumbnailUrl())
                <img src="{{ $workout->thumbnailUrl() }}" alt="" class="mb-3 h-40 w-full rounded-xl object-cover">
                <label class="mb-3 flex items-center gap-2 text-sm text-muted">
                    <input type="checkbox" name="remove_thumbnail" value="1" class="rounded border-border bg-bg text-primary focus:ring-primary">
                    Retirer le visuel
                </label>
            @endif
            <input id="thumbnail" name="thumbnail" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-muted file:mr-3 file:rounded-lg file:border-0 file:bg-surface file:px-3 file:py-2 file:text-sm file:font-medium file:text-text">
        </x-admin.form.field>
    </div>
</div>

@if ($exercises->isNotEmpty())
    <script>
        (() => {
            const list = document.getElementById('workout-items');
            const template = document.getElementById('workout-item-template');
            const addButton = document.getElementById('add-workout-item');
            if (!list || !template || !addButton) return;

            let nextIndex = list.querySelectorAll('.workout-item').length;

            const addItem = () => {
                const html = template.innerHTML.replaceAll('__INDEX__', String(nextIndex++));
                list.insertAdjacentHTML('beforeend', html);
            };

            addButton.addEventListener('click', addItem);

            list.addEventListener('click', (event) => {
                const item = event.target.closest('.workout-item');
                if (!item) return;

                if (event.target.closest('.js-item-remove')) {
                    if (list.querySelectorAll('.workout-item').length > 1) {
                        item.remove();
                    }
                    return;
                }

                if (event.target.closest('.js-item-up') && item.previousElementSibling) {
                    item.previousElementSibling.before(item);
                    return;
                }

                if (event.target.closest('.js-item-down') && item.nextElementSibling) {
                    item.nextElementSibling.after(item);
                }
            });
        })();
    </script>
@endif
