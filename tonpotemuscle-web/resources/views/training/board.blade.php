@php
    use App\Enums\EnrollmentStatus;
    use App\Models\ProgramWorkout;
    use Illuminate\Support\Carbon;

    $coach = $coach ?? false;
    $tabRoute = $tabRoute ?? 'dashboard';
    $tabParams = $tabParams ?? [];
    $firstName = explode(' ', trim($member->name))[0];
    $tabUrl = fn (string $vue, ?string $range = null) => route($tabRoute, array_filter(array_merge($tabParams, [
        'vue' => $vue,
        'week' => $monday->toDateString(),
        'range' => $vue === 'progres' ? ($range ?? 'month') : null,
    ])));
    $weekUrl = fn (Carbon $start) => route($tabRoute, array_merge($tabParams, [
        'vue' => $view,
        'week' => $start->toDateString(),
    ]));
    $maxBar = max(1, collect($progress['bars'])->max('count') ?: 0);
    $delta = $progress['count'] - $progress['previous'];
    $deltaLabel = match ($range) {
        'week' => 'que la semaine dernière',
        'year' => 'que l’année dernière',
        default => 'que le mois dernier',
    };
@endphp

<div class="space-y-6">
    <header class="flex flex-wrap items-start justify-between gap-3">
        <div>
            @if ($coach)
                <a
                    href="{{ route('admin.enrollments.index') }}"
                    class="inline-flex min-h-11 items-center rounded-lg border border-border px-3 text-sm font-medium text-text hover:border-primary hover:text-primary"
                >
                    ← Inscrits
                </a>
            @endif
            <h1 class="{{ $coach ? 'mt-4' : '' }} font-display text-3xl font-bold uppercase tracking-wide">Bonjour {{ $firstName }}.</h1>
            @if ($coach)
                <p class="mt-1 text-sm text-muted">
                    @if ($member->username)
                        {{ '@'.$member->username }} ·
                    @endif
                    {{ $member->email }}
                </p>
            @endif
        </div>
        @if ($coach && ! $member->isDeactivated())
            <a
                href="{{ route('admin.enrollments.give', $member) }}"
                class="inline-flex min-h-11 items-center rounded-lg bg-primary px-4 text-sm font-semibold text-bg hover:bg-primary-hover"
            >
                Donner
            </a>
        @endif
    </header>

    <div class="flex border border-border bg-surface p-1 text-sm">
        <a href="{{ $tabUrl('accueil') }}" class="flex min-h-11 flex-1 items-center justify-center px-3 font-medium {{ $view === 'accueil' ? 'bg-text text-bg' : 'text-muted hover:text-text' }}">Accueil</a>
        <a href="{{ $tabUrl('semaine') }}" class="flex min-h-11 flex-1 items-center justify-center px-3 font-medium {{ $view === 'semaine' ? 'bg-text text-bg' : 'text-muted hover:text-text' }}">Semaine</a>
        <a href="{{ $tabUrl('progres') }}" class="flex min-h-11 flex-1 items-center justify-center px-3 font-medium {{ $view === 'progres' ? 'bg-text text-bg' : 'text-muted hover:text-text' }}">Progrès</a>
    </div>

    @if ($view === 'accueil')
        <p class="text-sm text-muted">Cette semaine · {{ $weekDone }} séance{{ $weekDone > 1 ? 's' : '' }} sur {{ $weekWorkouts }}</p>

        <div class="grid grid-cols-7 gap-1">
            @foreach ($days as $day)
                <button
                    type="button"
                    class="js-day flex min-h-16 flex-col items-center justify-center border px-1 text-center {{ $day['is_today'] ? 'border-primary text-text' : 'border-border text-muted' }}"
                    data-day="{{ $day['key'] }}"
                >
                    <span class="text-[11px] font-semibold">{{ $day['letter'] }}</span>
                    <span class="mt-1 text-sm tabular-nums">{{ $day['date']->day }}</span>
                    @if ($day['status'] !== 'rest')
                        <span class="mt-1 h-1 w-3 {{ $day['done'] ? 'bg-primary' : 'bg-primary/50' }}"></span>
                    @endif
                </button>
            @endforeach
        </div>

        @foreach ($catchUp as $item)
            <article class="rounded-xl border border-border bg-surface p-5">
                <p class="text-[11px] font-bold uppercase tracking-wider text-primary">
                    À rattraper · {{ mb_strtoupper(mb_substr(ProgramWorkout::WEEKDAYS[$item->planned_on->dayOfWeekIso] ?? '', 0, 3)) }}. {{ $item->planned_on->day }}
                </p>
                @include('training._workout-detail', ['item' => $item])
            </article>
        @endforeach

        @foreach ($days as $day)
            <article class="js-day-panel rounded-xl border border-border bg-surface p-5 {{ $day['is_today'] ? '' : 'hidden' }}" data-day="{{ $day['key'] }}">
                @if ($day['item'] && ! $day['item']->isRest())
                    @if ($day['status'] === 'done')
                        <p class="mb-3 text-[11px] font-bold uppercase tracking-wider text-primary">
                            {{ $day['is_today'] ? 'Faite aujourd’hui' : 'Faite' }}
                        </p>
                    @elseif ($day['is_today'])
                        <p class="mb-3 text-[11px] font-bold uppercase tracking-wider text-primary">À faire aujourd’hui</p>
                    @elseif ($day['status'] === 'missed')
                        <p class="mb-3 text-[11px] font-bold uppercase tracking-wider text-danger">Manquée</p>
                    @endif
                    @include('training._workout-detail', ['item' => $day['item']])
                    @if (! $coach && $day['is_today'] && ! $day['done'])
                        <a
                            href="{{ route('member.workouts.show', $day['item']) }}"
                            class="mt-6 inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-primary px-4 font-display text-sm font-bold uppercase tracking-wider text-bg transition-colors hover:bg-primary-hover"
                        >
                            Débuter ma séance
                        </a>
                    @endif
                @else
                    <p class="text-sm text-muted">
                        @if ($day['is_today'] && $weekWorkouts === 0 && $catchUp->isEmpty())
                            {{ $coach ? 'Aucune séance donnée pour le moment.' : 'Ton coach ne t’a pas encore donné de séance.' }}
                        @else
                            {{ $day['is_today'] ? 'Repos aujourd’hui.' : 'Repos.' }}
                        @endif
                    </p>
                @endif
            </article>
        @endforeach

        @if ($enrollments->isNotEmpty())
            <section class="overflow-hidden rounded-xl border border-border bg-surface">
                <div class="border-b border-border px-5 py-4">
                    <h2 class="text-sm font-semibold text-text">Programmes</h2>
                </div>
                <ul class="divide-y divide-border">
                    @foreach ($enrollments as $enrollment)
                        <li class="flex items-center justify-between gap-4 px-5 py-3.5">
                            <p class="truncate text-sm font-medium text-text">{{ $enrollment->program?->title }}</p>
                            <p class="shrink-0 text-xs text-muted">
                                {{ $enrollment->status === EnrollmentStatus::Active ? 'Actif' : 'Abandon' }}
                                · {{ $enrollment->enrolled_at?->format('d/m/Y') }}
                            </p>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    @endif

    @if ($view === 'semaine')
        <p class="text-sm text-muted">Cette semaine</p>
        <div class="flex items-center justify-between text-sm text-muted">
            <a href="{{ $weekUrl($monday->copy()->subWeek()) }}" class="hover:text-text">Semaine précédente</a>
            <a href="{{ $weekUrl($monday->copy()->addWeek()) }}" class="hover:text-text">Semaine suivante</a>
        </div>

        <div class="overflow-hidden rounded-xl border border-border bg-surface">
            @foreach ($days as $day)
                <article class="border-b border-border px-5 py-4 last:border-b-0 {{ $day['is_today'] ? 'outline outline-1 outline-offset-[-1px] outline-primary' : '' }}">
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="font-display text-xl font-bold uppercase tracking-wide">
                            {{ $day['weekday'] }} {{ $day['date']->day }}
                            @if ($day['status'] === 'rest')
                                <span class="ml-2 text-sm font-medium normal-case tracking-normal text-muted">Repos</span>
                            @endif
                        </h2>
                        <div class="flex shrink-0 flex-col items-end gap-1">
                            @if ($day['is_today'])
                                <span class="bg-primary px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-bg">Aujourd’hui</span>
                            @endif
                            @if ($day['status'] === 'missed')
                                <span class="text-[10px] font-bold uppercase tracking-wider text-danger">Manquée</span>
                            @elseif ($day['status'] === 'today' || $day['status'] === 'todo')
                                <span class="text-[10px] font-bold uppercase tracking-wider text-primary">À faire</span>
                            @elseif ($day['status'] === 'done')
                                <span class="text-[10px] font-bold uppercase tracking-wider text-primary">Faite</span>
                            @endif
                        </div>
                    </div>
                    @if ($day['item'] && ! $day['item']->isRest())
                        <div class="mt-3 flex items-center gap-3">
                            <x-cover :src="$day['item']->workout?->thumbnailUrl()" icon="fa-solid fa-stopwatch" />
                            <p class="min-w-0 truncate text-sm font-medium text-text">{{ $day['item']->label() }}</p>
                        </div>
                        @if ($day['item']->workout?->items)
                            <ul class="mt-1 space-y-0.5 text-sm text-muted">
                                @foreach ($day['item']->workout->items as $row)
                                    <li>{{ $row->exercise?->title }}</li>
                                @endforeach
                            </ul>
                        @endif
                    @endif
                </article>
            @endforeach
        </div>
    @endif

    @if ($view === 'progres')
        <div class="flex border border-border bg-surface p-1 text-sm">
            <a href="{{ $tabUrl('progres', 'week') }}" class="flex min-h-10 flex-1 items-center justify-center px-3 font-medium {{ $range === 'week' ? 'bg-primary text-bg' : 'text-muted hover:text-text' }}">Semaine</a>
            <a href="{{ $tabUrl('progres', 'month') }}" class="flex min-h-10 flex-1 items-center justify-center px-3 font-medium {{ $range === 'month' ? 'bg-primary text-bg' : 'text-muted hover:text-text' }}">Mois</a>
            <a href="{{ $tabUrl('progres', 'year') }}" class="flex min-h-10 flex-1 items-center justify-center px-3 font-medium {{ $range === 'year' ? 'bg-primary text-bg' : 'text-muted hover:text-text' }}">Année</a>
        </div>

        <section class="rounded-xl border border-border bg-surface p-5">
            <p class="font-display text-5xl font-bold tabular-nums text-primary">{{ $progress['count'] }}</p>
            <p class="mt-1 text-sm text-muted">
                séance{{ $progress['count'] > 1 ? 's' : '' }}
                {{ $range === 'week' ? 'cette semaine' : ($range === 'year' ? 'cette année' : 'ce mois') }}
            </p>
            <p class="mt-2 text-sm text-text">
                @if ($delta === 0)
                    Pareil {{ $deltaLabel }}
                @elseif ($delta > 0)
                    {{ $delta }} de plus {{ $deltaLabel }}
                @else
                    {{ abs($delta) }} de moins {{ $deltaLabel }}
                @endif
            </p>
            <div class="mt-8 flex h-36 items-end gap-2">
                @foreach ($progress['bars'] as $bar)
                    <div class="flex min-w-0 flex-1 flex-col items-center justify-end gap-2">
                        <div class="w-full {{ $bar['current'] ? 'bg-primary' : 'bg-border' }}" style="height: {{ max(6, (int) round(($bar['count'] / $maxBar) * 120)) }}px"></div>
                        <span class="text-[10px] text-muted">{{ $bar['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section>
            <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-muted">Records</h2>
            <p class="mt-4 text-sm text-muted">
                @if ($coach)
                    Les charges max arriveront quand le membre déclarera ses séries.
                @else
                    Tes records de charge arriveront quand tu déclareras tes séries.
                @endif
            </p>
        </section>
    @endif
</div>

@if ($view === 'accueil')
    <script>
        (() => {
            const buttons = document.querySelectorAll('.js-day');
            const panels = document.querySelectorAll('.js-day-panel');
            buttons.forEach((button) => {
                button.addEventListener('click', () => {
                    buttons.forEach((item) => {
                        item.classList.toggle('border-primary', item === button);
                        item.classList.toggle('text-text', item === button);
                        item.classList.toggle('border-border', item !== button);
                        item.classList.toggle('text-muted', item !== button);
                    });
                    panels.forEach((panel) => {
                        panel.classList.toggle('hidden', panel.dataset.day !== button.dataset.day);
                    });
                });
            });
        })();
    </script>
@endif
