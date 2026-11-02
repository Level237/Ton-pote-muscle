@php
    $session = [
        'completeUrl' => route('member.workouts.complete', $item),
        'csrf' => csrf_token(),
        'exercises' => $workout->items->map(fn ($row) => [
            'workout_exercise_id' => $row->id,
            'exercise_id' => $row->exercise_id,
            'position' => $row->position,
            'title' => $row->exercise?->title ?? 'Exercice',
            'gif' => $row->exercise?->gifUrl(),
            'video' => $row->exercise?->demoUrl(),
            'sets' => max(1, (int) $row->sets),
            'reps' => $row->reps,
            'duration_seconds' => $row->duration_seconds,
            'rest_seconds' => max(0, (int) $row->rest_seconds),
            'work_label' => $row->workLabel(),
        ])->values()->all(),
    ];
@endphp

<x-layouts.live :title="$workout->title">
    <script type="application/json" id="live-session">@json($session)</script>

    <div id="live-workout" class="flex min-h-dvh flex-col">
        <header class="mx-auto w-full max-w-5xl px-4 pt-3 sm:px-6 sm:pt-5">
            <div class="flex items-center justify-between gap-4">
                <button type="button" data-live="quit" class="min-h-11 px-1 text-sm font-medium text-muted hover:text-text">
                    Quitter
                </button>
                <p data-live="progress" class="text-xs font-semibold tabular-nums text-muted"></p>
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        data-live="sound"
                        aria-label="Son"
                        class="flex h-11 w-11 items-center justify-center rounded-lg text-sm text-muted hover:text-text"
                    >
                        <i data-live="sound-icon" class="fa-solid fa-volume-high"></i>
                    </button>
                    <button type="button" data-live="skip" class="min-h-11 px-1 text-sm font-medium text-muted hover:text-text">
                        Passer
                    </button>
                </div>
            </div>

            <div class="mt-2 flex gap-1.5">
                @foreach ($workout->items as $row)
                    <span data-live="step" class="h-1 flex-1 rounded-full bg-border"></span>
                @endforeach
            </div>
        </header>

        <main class="mx-auto flex w-full max-w-5xl flex-1 flex-col justify-center gap-6 px-4 py-6 sm:px-6 lg:grid lg:grid-cols-12 lg:items-center lg:gap-10">
            <div data-live="visual" class="hidden items-center justify-center rounded-2xl bg-surface p-4 lg:col-span-6 lg:p-8">
                <img data-live="gif" alt="" class="max-h-56 w-full object-contain sm:max-h-72 lg:max-h-80">
            </div>

            <div data-live="panel" class="flex flex-col items-center text-center lg:col-span-12">
                <p data-live="phase" class="text-xs font-bold uppercase tracking-wider text-primary"></p>
                <h1 data-live="title" class="mt-2 text-2xl font-bold text-text sm:text-3xl"></h1>
                <p data-live="meta" class="mt-1 text-sm text-muted"></p>
                <a
                    data-live="demo"
                    href="#"
                    target="_blank"
                    rel="noreferrer"
                    class="mt-3 hidden text-sm font-medium text-primary hover:text-primary-hover"
                >
                    Voir la démo
                </a>

                <p
                    data-live="timer"
                    class="mt-6 font-display text-7xl font-bold tabular-nums leading-none sm:text-8xl"
                    aria-live="polite"
                ></p>
                <p data-live="unit" class="mt-2 text-xs uppercase tracking-wider text-muted"></p>
            </div>
        </main>

        <footer class="mx-auto w-full max-w-xl px-4 pb-[max(1.25rem,env(safe-area-inset-bottom))] sm:px-6">
            <p data-live="upcoming" class="mb-3 truncate text-center text-xs text-muted"></p>

            <div class="grid grid-cols-2 gap-3">
                <button
                    type="button"
                    data-live="pause"
                    class="inline-flex min-h-12 items-center justify-center rounded-xl border border-border bg-surface px-4 text-sm font-semibold text-text"
                >
                    Pause
                </button>
                <button
                    type="button"
                    data-live="next"
                    class="inline-flex min-h-12 items-center justify-center rounded-xl bg-primary px-4 text-sm font-bold text-bg"
                >
                    Suivant
                </button>
            </div>
        </footer>
    </div>
</x-layouts.live>
