@php
    use App\Support\WorkoutCatalog;

    $inputClass = 'min-h-11 w-full rounded-lg border border-border bg-bg px-3 text-sm text-text outline-none placeholder:text-muted/50 focus:border-primary focus:ring-2 focus:ring-primary';
    $exercises = $exercises ?? collect();
    $drawerItem = [
        'exercise_id' => '',
        'sets' => 1,
        'reps' => '',
        'duration_seconds' => '',
        'rest_seconds' => 30,
    ];
@endphp

<dialog
    id="inline-workout-dialog"
    class="program-workout-drawer"
    aria-labelledby="inline-workout-heading"
>
    <form id="inline-workout-form" class="flex h-full min-h-0 flex-col">
        <header class="relative shrink-0 border-b border-border px-6 py-5 pr-14">
            <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted">Composer</p>
            <h2 id="inline-workout-heading" class="mt-1 text-xl font-semibold text-text">Nouvelle séance</h2>
            <p class="mt-1 text-sm text-muted">Elle rejoint le catalogue, puis ce jour du programme.</p>
            <button type="button" class="js-inline-workout-cancel absolute top-4 right-4 flex h-11 w-11 items-center justify-center text-muted hover:text-text" aria-label="Fermer">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </header>

        <div class="min-h-0 flex-1 overflow-y-auto px-6 py-6">
            <div id="inline-workout-errors" class="mb-5 hidden rounded-xl border border-danger/40 bg-danger/10 px-4 py-3 text-sm text-danger"></div>

                <div class="space-y-6">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-text" for="inline-workout-title">Titre</label>
                        <input
                            id="inline-workout-title"
                            name="title"
                            type="text"
                            required
                            maxlength="120"
                            placeholder="Full body A"
                            class="{{ $inputClass }}"
                        >
                    </div>

                    <fieldset>
                        <legend class="mb-2 text-sm font-medium text-text">Niveau</legend>
                        <div class="flex flex-wrap gap-2">
                            @foreach (WorkoutCatalog::DIFFICULTIES as $value => $optionLabel)
                                <label class="cursor-pointer">
                                    <input type="radio" name="difficulty" value="{{ $value }}" class="peer sr-only">
                                    <span class="inline-flex min-h-11 items-center rounded-full border border-border px-3.5 text-sm text-muted transition-colors peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-primary">
                                        {{ $optionLabel }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <div>
                        <div class="mb-3 flex items-end justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-medium text-text">Composition</h3>
                                <p class="text-xs text-muted">Ordre = déroulé du live</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <button type="button" class="js-create-exercise text-sm font-medium text-primary hover:text-primary-hover">
                                    + Nouvel
                                </button>
                                <button type="button" id="inline-add-workout-item" class="text-sm font-medium text-primary hover:text-primary-hover">
                                    + Exercice
                                </button>
                            </div>
                        </div>
                        <ul id="inline-workout-items" class="space-y-2" data-workout-items>
                            @include('admin.programs._drawer-item', [
                                'index' => 0,
                                'idPrefix' => 'inline-',
                                'item' => $drawerItem,
                                'exercises' => $exercises,
                            ])
                        </ul>
                        <template id="inline-workout-item-template">
                            @include('admin.programs._drawer-item', [
                                'index' => '__INDEX__',
                                'idPrefix' => 'inline-',
                                'item' => $drawerItem,
                                'exercises' => $exercises,
                            ])
                        </template>
                    </div>
                </div>
        </div>

            <div class="shrink-0 border-t border-border bg-surface px-6 py-4">
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" class="js-inline-workout-cancel inline-flex min-h-11 items-center justify-center rounded-lg border border-border px-4 text-sm font-medium text-text hover:border-primary hover:text-primary">
                        Annuler
                    </button>
                    <button type="submit" id="inline-workout-submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-primary px-4 text-sm font-semibold text-bg hover:bg-primary-hover">
                        Créer et ajouter
                    </button>
                </div>
            </div>
    </form>
</dialog>

<script>
    (() => {
        const CREATE_VALUE = '__new__';
        const dialog = document.getElementById('inline-workout-dialog');
        const form = document.getElementById('inline-workout-form');
        const errors = document.getElementById('inline-workout-errors');
        const itemsList = document.getElementById('inline-workout-items');
        const itemTemplate = document.getElementById('inline-workout-item-template');
        const addItemButton = document.getElementById('inline-add-workout-item');
        const submitButton = document.getElementById('inline-workout-submit');
        if (!dialog || !form) return;

        let targetSelect = null;
        let nextItemIndex = itemsList ? itemsList.querySelectorAll('.workout-item').length : 1;

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const storeUrl = @json(route('admin.workouts.store'));

        const showErrors = (messages) => {
            if (!errors) return;
            const list = (Array.isArray(messages) ? messages : [messages]).filter(Boolean);
            errors.replaceChildren();
            list.forEach((message) => {
                const line = document.createElement('p');
                line.textContent = message;
                errors.appendChild(line);
            });
            errors.classList.toggle('hidden', list.length === 0);
        };

        const resetForm = () => {
            form.reset();
            showErrors([]);
            if (!itemsList || !itemTemplate) return;
            itemsList.innerHTML = itemTemplate.innerHTML.replaceAll('__INDEX__', '0');
            nextItemIndex = 1;
        };

        const addWorkoutOption = (id, title, select) => {
            const apply = (root) => {
                root.querySelectorAll('.js-slot-workout').forEach((node) => {
                    if ([...node.options].some((option) => option.value === id)) return;
                    const option = new Option(title, id);
                    const createOption = [...node.options].find((item) => item.value === CREATE_VALUE);
                    if (createOption) {
                        node.add(option, createOption);
                    } else {
                        node.add(option);
                    }
                });
            };

            apply(document);
            const slotTemplate = document.getElementById('program-slot-template');
            if (slotTemplate) apply(slotTemplate.content);

            if (select) select.value = id;
        };

        const payloadFromForm = () => ({
            title: form.querySelector('[name="title"]')?.value ?? '',
            difficulty: form.querySelector('[name="difficulty"]:checked')?.value || null,
            items: [...(itemsList?.querySelectorAll('.workout-item') ?? [])].map((item) => ({
                exercise_id: item.querySelector('[name*="[exercise_id]"]')?.value ?? '',
                sets: item.querySelector('[name*="[sets]"]')?.value ?? 1,
                reps: item.querySelector('[name*="[reps]"]')?.value ?? '',
                duration_seconds: item.querySelector('[name*="[duration_seconds]"]')?.value ?? '',
                rest_seconds: item.querySelector('[name*="[rest_seconds]"]')?.value ?? 30,
            })),
        });

        window.programWorkoutComposer = {
            open(select) {
                targetSelect = select ?? null;
                resetForm();
                dialog.showModal();
                form.querySelector('[name="title"]')?.focus();
            },
        };

        addItemButton?.addEventListener('click', () => {
            if (!itemsList || !itemTemplate) return;
            itemsList.insertAdjacentHTML('beforeend', itemTemplate.innerHTML.replaceAll('__INDEX__', String(nextItemIndex++)));
        });

        itemsList?.addEventListener('click', (event) => {
            const item = event.target.closest('.workout-item');
            if (!item) return;

            if (event.target.closest('.js-item-remove')) {
                if (itemsList.querySelectorAll('.workout-item').length > 1) {
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

        form.querySelectorAll('.js-inline-workout-cancel').forEach((button) => {
            button.addEventListener('click', () => dialog.close());
        });

        dialog.addEventListener('close', () => {
            targetSelect = null;
            if (submitButton) submitButton.disabled = false;
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (!submitButton) return;

            submitButton.disabled = true;
            showErrors([]);

            try {
                const response = await fetch(storeUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(payloadFromForm()),
                });

                const data = await response.json().catch(() => ({}));

                if (response.status === 422) {
                    const messages = Object.values(data.errors ?? {}).flat();
                    showErrors(messages.length ? messages : [data.message || 'Vérifie les champs.']);
                    submitButton.disabled = false;
                    errors?.scrollIntoView({ block: 'nearest' });
                    return;
                }

                if (!response.ok || !data.id) {
                    showErrors(data.message || 'Impossible de créer la séance.');
                    submitButton.disabled = false;
                    return;
                }

                addWorkoutOption(data.id, data.title, targetSelect);
                dialog.close();
            } catch {
                showErrors('Impossible de créer la séance.');
                submitButton.disabled = false;
            }
        });
    })();
</script>
