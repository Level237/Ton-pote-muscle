@php
    use App\Support\ExerciseCatalog;

    $inputClass = 'min-h-11 w-full rounded-lg border border-border bg-bg px-3 text-sm text-text outline-none placeholder:text-muted/50 focus:border-primary focus:ring-2 focus:ring-primary';
@endphp

<dialog
    id="inline-exercise-dialog"
    class="program-workout-drawer"
    aria-labelledby="inline-exercise-heading"
>
    <form id="inline-exercise-form" class="flex h-full min-h-0 flex-col">
        <header class="relative shrink-0 border-b border-border px-6 py-5 pr-14">
            <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted">Catalogue</p>
            <h2 id="inline-exercise-heading" class="mt-1 text-xl font-semibold text-text">Nouvel exercice</h2>
            <p class="mt-1 text-sm text-muted">Il rejoint le catalogue, puis cette séance.</p>
            <button type="button" class="js-inline-exercise-cancel absolute top-4 right-4 flex h-11 w-11 items-center justify-center text-muted hover:text-text" aria-label="Fermer">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </header>

        <div class="min-h-0 flex-1 overflow-y-auto px-6 py-6">
            <div id="inline-exercise-errors" class="mb-5 hidden rounded-xl border border-danger/40 bg-danger/10 px-4 py-3 text-sm text-danger"></div>

            <div class="space-y-6">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-text" for="inline-exercise-title">Titre</label>
                    <input
                        id="inline-exercise-title"
                        name="title"
                        type="text"
                        required
                        maxlength="120"
                        placeholder="Pompes"
                        class="{{ $inputClass }}"
                    >
                </div>

                <fieldset>
                    <legend class="mb-2 text-sm font-medium text-text">Zones</legend>
                    <div class="flex flex-wrap gap-2">
                        @foreach (ExerciseCatalog::BODY_PARTS as $value => $optionLabel)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="body_parts[]" value="{{ $value }}" class="peer sr-only">
                                <span class="inline-flex min-h-11 items-center rounded-full border border-border px-3.5 text-sm text-muted transition-colors peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-primary">
                                    {{ $optionLabel }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="mb-2 text-sm font-medium text-text">Matériel</legend>
                    <div class="flex flex-wrap gap-2">
                        @foreach (ExerciseCatalog::EQUIPMENTS as $value => $optionLabel)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="equipments[]" value="{{ $value }}" class="peer sr-only">
                                <span class="inline-flex min-h-11 items-center rounded-full border border-border px-3.5 text-sm text-muted transition-colors peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-primary">
                                    {{ $optionLabel }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-text" for="inline-exercise-instructions">Consignes</label>
                    <p class="mb-1.5 text-xs text-muted">Une étape par ligne.</p>
                    <textarea
                        id="inline-exercise-instructions"
                        name="instructions"
                        rows="5"
                        maxlength="5000"
                        class="{{ $inputClass }} py-2.5"
                    ></textarea>
                </div>
            </div>
        </div>

        <div class="shrink-0 border-t border-border bg-surface px-6 py-4">
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button type="button" class="js-inline-exercise-cancel inline-flex min-h-11 items-center justify-center rounded-lg border border-border px-4 text-sm font-medium text-text hover:border-primary hover:text-primary">
                    Annuler
                </button>
                <button type="submit" id="inline-exercise-submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-primary px-4 text-sm font-semibold text-bg hover:bg-primary-hover">
                    Créer et ajouter
                </button>
            </div>
        </div>
    </form>
</dialog>

<script>
    (() => {
        const CREATE_VALUE = '__new__';
        const dialog = document.getElementById('inline-exercise-dialog');
        const form = document.getElementById('inline-exercise-form');
        const errors = document.getElementById('inline-exercise-errors');
        const submitButton = document.getElementById('inline-exercise-submit');
        if (!dialog || !form) return;

        let targetSelect = null;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const storeUrl = @json(route('admin.exercises.store'));

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

        const addExerciseOption = (id, title, select) => {
            const apply = (root) => {
                root.querySelectorAll('.js-exercise-select').forEach((node) => {
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
            ['workout-item-template', 'inline-workout-item-template'].forEach((templateId) => {
                const template = document.getElementById(templateId);
                if (template) apply(template.content);
            });

            if (select) select.value = id;
        };

        const payloadFromForm = () => ({
            title: form.querySelector('[name="title"]')?.value ?? '',
            body_parts: [...form.querySelectorAll('[name="body_parts[]"]:checked')].map((input) => input.value),
            equipments: [...form.querySelectorAll('[name="equipments[]"]:checked')].map((input) => input.value),
            instructions: form.querySelector('[name="instructions"]')?.value ?? '',
        });

        const open = (select) => {
            targetSelect = select ?? null;
            form.reset();
            showErrors([]);
            dialog.showModal();
            form.querySelector('[name="title"]')?.focus();
        };

        window.workoutExerciseComposer = { open };

        document.addEventListener('focusin', (event) => {
            if (!event.target.classList.contains('js-exercise-select')) return;
            event.target.dataset.previousValue = event.target.value;
        });

        document.addEventListener('change', (event) => {
            if (!event.target.classList.contains('js-exercise-select')) return;
            if (event.target.value !== CREATE_VALUE) return;
            event.target.value = event.target.dataset.previousValue || '';
            open(event.target);
        });

        document.addEventListener('click', (event) => {
            const button = event.target.closest('.js-create-exercise');
            if (!button) return;
            const row = button.closest('.workout-item');
            const list = button.closest('[data-workout-items]') || document.getElementById('inline-workout-items') || document.getElementById('workout-items');
            const select = row?.querySelector('.js-exercise-select')
                || [...(list?.querySelectorAll('.js-exercise-select') ?? [])].find((node) => !node.value)
                || list?.querySelector('.js-exercise-select');
            open(select);
        });

        form.querySelectorAll('.js-inline-exercise-cancel').forEach((button) => {
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
                    showErrors(data.message || 'Impossible de créer l’exercice.');
                    submitButton.disabled = false;
                    return;
                }

                addExerciseOption(data.id, data.title, targetSelect);
                dialog.close();
            } catch {
                showErrors('Impossible de créer l’exercice.');
                submitButton.disabled = false;
            }
        });
    })();
</script>
