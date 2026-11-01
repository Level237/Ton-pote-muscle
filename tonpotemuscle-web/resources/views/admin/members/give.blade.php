@php
    use App\Support\ProgramWeeks;

    $inputClass = 'min-h-11 w-full rounded-lg border border-border bg-bg px-3 text-sm text-text outline-none focus:border-primary focus:ring-2 focus:ring-primary';
    $mode = old('mode', 'workout');
    $firstName = explode(' ', trim($member->name))[0];
    $selectedCategory = old('category', '');
    $selectedDays = collect(old('planned_on', [now()->toDateString()]))->filter()->values();
@endphp

<x-layouts.admin title="Donner">
    <div class="mx-auto max-w-xl space-y-6">
        <header>
            <a href="{{ route('admin.enrollments.show', $member) }}" class="text-sm text-muted hover:text-primary">{{ $member->name }}</a>
            <h1 class="mt-1 font-display text-2xl font-bold uppercase tracking-wide">Donner</h1>
        </header>

        <form method="POST" action="{{ route('admin.enrollments.give.store', $member) }}" class="space-y-6" id="give-form">
            @csrf

            <div class="flex border border-border bg-surface p-1 text-sm">
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="mode" value="workout" class="peer sr-only" @checked($mode === 'workout')>
                    <span class="flex min-h-11 items-center justify-center px-3 font-medium text-muted peer-checked:bg-text peer-checked:text-bg">Une séance</span>
                </label>
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="mode" value="program" class="peer sr-only" @checked($mode === 'program')>
                    <span class="flex min-h-11 items-center justify-center px-3 font-medium text-muted peer-checked:bg-text peer-checked:text-bg">Une semaine</span>
                </label>
            </div>
            @error('mode')
                <p class="text-sm text-danger">{{ $message }}</p>
            @enderror

            <div id="panel-workout" class="{{ $mode === 'workout' ? '' : 'hidden' }} space-y-5 rounded-xl border border-border bg-surface p-5">
                <div class="flex flex-wrap gap-1">
                    <label class="cursor-pointer">
                        <input type="radio" name="category" value="" class="js-category peer sr-only" @checked($selectedCategory === '')>
                        <span class="flex min-h-10 items-center px-3 text-sm font-medium text-muted peer-checked:bg-text peer-checked:text-bg">Toutes</span>
                    </label>
                    @foreach ($categories as $category)
                        <label class="cursor-pointer">
                            <input type="radio" name="category" value="{{ $category->id }}" class="js-category peer sr-only" @checked((string) $selectedCategory === (string) $category->id)>
                            <span class="flex min-h-10 items-center px-3 text-sm font-medium text-muted peer-checked:bg-text peer-checked:text-bg">{{ $category->title }}</span>
                        </label>
                    @endforeach
                </div>

                <div>
                    <p class="mb-2 text-sm text-muted">Séance</p>
                    @if ($workouts->isEmpty())
                        <p class="text-sm text-muted">Aucune séance au catalogue.</p>
                    @else
                        <select id="workout_id" name="workout_id" class="{{ $inputClass }}">
                            <option value="">Choisir une séance...</option>
                            @foreach ($workouts as $workout)
                                <option
                                    value="{{ $workout->id }}"
                                    data-category="{{ $workout->category_id }}"
                                    @selected((string) old('workout_id') === (string) $workout->id)
                                >
                                    {{ $workout->title }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('workout_id')
                        <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <p class="mb-2 text-sm text-muted">Pour les jours</p>
                    <div class="mb-3 flex items-center justify-between gap-3 text-sm text-muted">
                        <button type="button" id="week-prev" class="hover:text-text">← Semaine précédente</button>
                        <button type="button" id="week-next" class="hover:text-text">Semaine suivante →</button>
                    </div>
                    <div id="week-days" class="grid grid-cols-7 gap-1"></div>
                    <div id="planned-on-inputs"></div>
                    @error('planned_on')
                        <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                    @enderror
                    @error('planned_on.*')
                        <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" id="give-workout-submit" class="inline-flex min-h-11 w-full items-center justify-center bg-primary px-4 font-display text-sm font-bold uppercase tracking-wider text-bg hover:bg-primary-hover">
                    Donner 1 séance
                </button>
            </div>

            <div id="panel-program" class="{{ $mode === 'program' ? '' : 'hidden' }} space-y-5 rounded-xl border border-border bg-surface p-5">
                <p class="text-sm text-muted">Quelle semaine donner à {{ $firstName }} ?</p>

                <div class="space-y-3">
                    @if ($currentEnrollment?->program)
                        <label class="block cursor-pointer">
                            <input
                                type="radio"
                                name="program_id"
                                value="current"
                                class="peer sr-only"
                                @checked(old('program_id') === 'current')
                            >
                            <span class="block rounded-lg border border-border px-4 py-3 peer-checked:border-primary">
                                <span class="block text-sm font-semibold text-text">Sa semaine actuelle</span>
                                <span class="mt-1 block text-xs text-muted">
                                    {{ $currentEnrollment->program->title }} · semaine {{ $currentWeek }}
                                </span>
                            </span>
                        </label>
                    @endif

                    @forelse ($programs as $item)
                        @php
                            $program = $item['program'];
                            $selected = (string) old('program_id') === (string) $program->id;
                            $selectedWeek = (int) old('program_week', 1);
                            if ($selectedWeek < 1 || $selectedWeek > $item['week_count']) {
                                $selectedWeek = 1;
                            }
                        @endphp
                        <label class="block cursor-pointer">
                            <input
                                type="radio"
                                name="program_id"
                                value="{{ $program->id }}"
                                class="js-program-choice peer sr-only"
                                data-program="{{ $program->id }}"
                                @checked($selected)
                            >
                            <span class="block rounded-lg border border-border px-4 py-3 peer-checked:border-primary">
                                <span class="block text-sm font-semibold text-text">{{ $program->title }}</span>
                                @if ($item['week_count'] > 1)
                                    <span class="mt-3 flex flex-wrap gap-1">
                                        @for ($week = 1; $week <= $item['week_count']; $week++)
                                            <button
                                                type="button"
                                                class="js-week-pick rounded-md border border-border px-2 py-1 text-xs text-muted hover:text-text"
                                                data-program="{{ $program->id }}"
                                                data-week="{{ $week }}"
                                            >
                                                S{{ $week }}
                                            </button>
                                        @endfor
                                    </span>
                                @endif
                                @foreach ($item['weeks'] as $week => $days)
                                    <span class="js-week-preview mt-3 block space-y-1 text-sm {{ $week === $selectedWeek ? '' : 'hidden' }}" data-program="{{ $program->id }}" data-week="{{ $week }}">
                                        @foreach ($days as $day)
                                            <span class="flex gap-2 {{ $day['is_rest'] ? 'text-muted' : 'text-text' }}">
                                                <span class="w-10 shrink-0 text-muted">{{ $day['weekday_short'] }}</span>
                                                <span>{{ $day['day_number'] }} · {{ $day['label'] }}</span>
                                            </span>
                                        @endforeach
                                    </span>
                                @endforeach
                            </span>
                        </label>
                    @empty
                        <p class="text-sm text-muted">Aucun programme ouvert.</p>
                    @endforelse
                    @error('program_id')
                        <p class="text-sm text-danger">{{ $message }}</p>
                    @enderror
                    <input type="hidden" name="program_week" id="program_week" value="{{ old('program_week', 1) }}">
                </div>

                <div>
                    <p class="mb-3 text-sm text-muted">Pour</p>
                    <div class="space-y-2">
                        <label class="block cursor-pointer">
                            <input type="radio" name="target_week" value="next" class="peer sr-only" @checked(old('target_week', 'next') === 'next')>
                            <span class="block rounded-lg border border-border px-4 py-3 peer-checked:border-primary">
                                <span class="block text-sm font-semibold text-text">La semaine prochaine</span>
                                <span class="mt-1 block text-xs text-muted">Semaine du {{ ProgramWeeks::frenchDay($nextMonday) }}</span>
                            </span>
                        </label>
                        <label class="block cursor-pointer">
                            <input type="radio" name="target_week" value="this" class="peer sr-only" @checked(old('target_week') === 'this')>
                            <span class="block rounded-lg border border-border px-4 py-3 peer-checked:border-primary">
                                <span class="block text-sm font-semibold text-text">Cette semaine</span>
                                <span class="mt-1 block text-xs text-muted">À partir d’aujourd’hui</span>
                            </span>
                        </label>
                    </div>
                    @error('target_week')
                        <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit" id="give-program-submit" class="inline-flex min-h-11 w-full items-center justify-center bg-primary px-4 font-display text-sm font-bold uppercase tracking-wider text-bg hover:bg-primary-hover">
                Donner
            </button>
        </form>

        <section class="space-y-3">
            <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-muted">Historique</h2>
            @forelse ($history as $item)
                <article class="border-t border-border py-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-text">{{ $item->label() }}</p>
                            <p class="mt-0.5 text-xs text-muted">Prévue {{ ProgramWeeks::frenchLongDay($item->planned_on) }}</p>
                        </div>
                        <span class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-primary">À faire</span>
                    </div>
                    <form method="POST" action="{{ route('admin.enrollments.plan.destroy', [$member, $item]) }}" class="mt-3">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm font-medium text-text hover:text-primary">Retirer</button>
                    </form>
                </article>
            @empty
                <p class="text-sm text-muted">Aucune séance donnée pour le moment.</p>
            @endforelse
        </section>
    </div>

    <script>
        (() => {
            const form = document.getElementById('give-form');
            if (!form) return;

            const workoutPanel = document.getElementById('panel-workout');
            const programPanel = document.getElementById('panel-program');
            const weekInput = document.getElementById('program_week');
            const workoutSubmit = document.getElementById('give-workout-submit');
            const programSubmit = document.getElementById('give-program-submit');
            const workoutSelect = document.getElementById('workout_id');
            const weekDays = document.getElementById('week-days');
            const plannedInputs = document.getElementById('planned-on-inputs');
            const weekdayShort = ['dim.', 'lun.', 'mar.', 'mer.', 'jeu.', 'ven.', 'sam.'];
            const iso = (date) => {
                const y = date.getFullYear();
                const m = String(date.getMonth() + 1).padStart(2, '0');
                const d = String(date.getDate()).padStart(2, '0');
                return `${y}-${m}-${d}`;
            };
            const startOfSundayWeek = (date) => {
                const start = new Date(date.getFullYear(), date.getMonth(), date.getDate());
                start.setDate(start.getDate() - start.getDay());
                return start;
            };
            const selectedDays = new Set(@json($selectedDays->all()));
            if (selectedDays.size === 0) {
                selectedDays.add(iso(new Date()));
            }
            let weekStart = startOfSundayWeek(new Date(`${[...selectedDays][0]}T00:00:00`));

            const syncMode = () => {
                const mode = form.querySelector('input[name="mode"]:checked')?.value;
                workoutPanel?.classList.toggle('hidden', mode !== 'workout');
                programPanel?.classList.toggle('hidden', mode !== 'program');
                workoutSubmit?.classList.toggle('hidden', mode !== 'workout');
                programSubmit?.classList.toggle('hidden', mode !== 'program');
            };

            const filterWorkouts = () => {
                if (!workoutSelect) return;
                const category = form.querySelector('input[name="category"]:checked')?.value || '';
                const current = workoutSelect.value;
                let visible = 0;
                Array.from(workoutSelect.options).forEach((option) => {
                    if (!option.value) {
                        option.hidden = false;
                        return;
                    }
                    const match = category === '' || option.dataset.category === category;
                    option.hidden = !match;
                    if (match) visible += 1;
                });
                const selected = workoutSelect.options[workoutSelect.selectedIndex];
                if (selected && selected.hidden) {
                    workoutSelect.value = '';
                }
                if (category !== '' && visible === 0) {
                    workoutSelect.value = '';
                }
                void current;
            };

            const syncGiveLabel = () => {
                if (!workoutSubmit) return;
                const count = Math.max(1, selectedDays.size);
                workoutSubmit.textContent = count > 1 ? `Donner ${count} séances` : 'Donner 1 séance';
            };

            const renderWeek = () => {
                if (!weekDays || !plannedInputs) return;
                weekDays.innerHTML = '';
                plannedInputs.innerHTML = '';
                const today = iso(new Date());

                for (let i = 0; i < 7; i += 1) {
                    const date = new Date(weekStart);
                    date.setDate(weekStart.getDate() + i);
                    const value = iso(date);
                    const selected = selectedDays.has(value);
                    const isToday = value === today;
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = `flex min-h-16 flex-col items-center justify-center border px-1 text-center text-xs ${selected ? 'border-primary text-text' : 'border-border text-muted'}`;
                    button.dataset.date = value;
                    button.innerHTML = `<span>${isToday ? 'auj.' : weekdayShort[date.getDay()]}</span><span class="mt-1 text-sm tabular-nums">${date.getDate()}</span>`;
                    button.addEventListener('click', () => {
                        if (selectedDays.has(value)) {
                            if (selectedDays.size === 1) return;
                            selectedDays.delete(value);
                        } else {
                            selectedDays.add(value);
                        }
                        renderWeek();
                    });
                    weekDays.appendChild(button);

                    if (selected) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'planned_on[]';
                        input.value = value;
                        plannedInputs.appendChild(input);
                    }
                }

                syncGiveLabel();
            };

            const showWeek = (programId, week) => {
                document.querySelectorAll('.js-week-preview').forEach((preview) => {
                    if (preview.dataset.program !== programId) return;
                    preview.classList.toggle('hidden', preview.dataset.week !== String(week));
                });
                document.querySelectorAll('.js-week-pick').forEach((button) => {
                    if (button.dataset.program !== programId) return;
                    const active = button.dataset.week === String(week);
                    button.classList.toggle('border-primary', active);
                    button.classList.toggle('text-primary', active);
                    button.classList.toggle('text-muted', !active);
                });
                if (weekInput) weekInput.value = String(week);
            };

            form.addEventListener('change', (event) => {
                if (event.target.name === 'mode') syncMode();
                if (event.target.name === 'category') filterWorkouts();
                if (event.target.classList.contains('js-program-choice')) {
                    showWeek(event.target.dataset.program, weekInput?.value || '1');
                }
            });

            form.addEventListener('click', (event) => {
                const button = event.target.closest('.js-week-pick');
                if (!button) return;
                event.preventDefault();
                const radio = form.querySelector(`.js-program-choice[data-program="${button.dataset.program}"]`);
                if (radio) radio.checked = true;
                showWeek(button.dataset.program, button.dataset.week);
            });

            document.getElementById('week-prev')?.addEventListener('click', () => {
                weekStart.setDate(weekStart.getDate() - 7);
                renderWeek();
            });
            document.getElementById('week-next')?.addEventListener('click', () => {
                weekStart.setDate(weekStart.getDate() + 7);
                renderWeek();
            });

            syncMode();
            filterWorkouts();
            renderWeek();
            const selected = form.querySelector('.js-program-choice:checked');
            if (selected) showWeek(selected.dataset.program, weekInput?.value || '1');
        })();
    </script>
</x-layouts.admin>
