@php
    $metricClass = 'min-h-11 w-full rounded-lg border border-border bg-bg px-2.5 text-center text-sm tabular-nums text-text outline-none focus:border-primary focus:ring-2 focus:ring-primary';
    $idPrefix = $idPrefix ?? 'inline-';
@endphp

<li class="workout-item rounded-xl border border-border bg-bg/80 p-3">
    <div class="flex items-center gap-3">
        <span class="js-item-index w-8 shrink-0 font-display text-lg font-semibold tabular-nums text-muted" aria-hidden="true"></span>
        <div class="min-w-0 flex-1">
            <label class="sr-only" for="{{ $idPrefix }}items-{{ $index }}-exercise">Exercice</label>
            <select
                id="{{ $idPrefix }}items-{{ $index }}-exercise"
                name="items[{{ $index }}][exercise_id]"
                class="js-exercise-select min-h-11 w-full rounded-lg border border-border bg-bg px-3 text-sm text-text outline-none focus:border-primary focus:ring-2 focus:ring-primary"
            >
                <option value="">Choisir un exercice</option>
                @foreach ($exercises as $exercise)
                    <option value="{{ $exercise->id }}" @selected((string) ($item['exercise_id'] ?? '') === (string) $exercise->id)>
                        {{ $exercise->title }}
                    </option>
                @endforeach
                <option value="__new__">+ Nouvel exercice</option>
            </select>
        </div>
        <div class="flex shrink-0 items-center">
            <button type="button" class="js-item-up flex h-11 w-9 items-center justify-center text-muted hover:text-text" aria-label="Monter">
                <i class="fa-solid fa-chevron-up text-xs" aria-hidden="true"></i>
            </button>
            <button type="button" class="js-item-down flex h-11 w-9 items-center justify-center text-muted hover:text-text" aria-label="Descendre">
                <i class="fa-solid fa-chevron-down text-xs" aria-hidden="true"></i>
            </button>
            <button type="button" class="js-item-remove flex h-11 w-9 items-center justify-center text-muted hover:text-danger" aria-label="Retirer">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <div class="mt-3 grid grid-cols-4 gap-2 pl-11">
        <label class="block">
            <span class="mb-1 block text-[11px] font-medium uppercase tracking-wider text-muted">Séries</span>
            <input
                id="{{ $idPrefix }}items-{{ $index }}-sets"
                name="items[{{ $index }}][sets]"
                type="number"
                min="1"
                max="20"
                value="{{ $item['sets'] ?? 1 }}"
                required
                class="{{ $metricClass }}"
            >
        </label>
        <label class="block">
            <span class="mb-1 block text-[11px] font-medium uppercase tracking-wider text-muted">Reps</span>
            <input
                id="{{ $idPrefix }}items-{{ $index }}-reps"
                name="items[{{ $index }}][reps]"
                type="number"
                min="1"
                max="999"
                value="{{ $item['reps'] ?? '' }}"
                placeholder="—"
                class="{{ $metricClass }}"
            >
        </label>
        <label class="block">
            <span class="mb-1 block text-[11px] font-medium uppercase tracking-wider text-muted">Sec</span>
            <input
                id="{{ $idPrefix }}items-{{ $index }}-duration"
                name="items[{{ $index }}][duration_seconds]"
                type="number"
                min="1"
                max="3600"
                value="{{ $item['duration_seconds'] ?? '' }}"
                placeholder="—"
                class="{{ $metricClass }}"
            >
        </label>
        <label class="block">
            <span class="mb-1 block text-[11px] font-medium uppercase tracking-wider text-muted">Repos</span>
            <input
                id="{{ $idPrefix }}items-{{ $index }}-rest"
                name="items[{{ $index }}][rest_seconds]"
                type="number"
                min="0"
                max="600"
                value="{{ $item['rest_seconds'] ?? 30 }}"
                required
                class="{{ $metricClass }}"
            >
        </label>
    </div>
</li>
