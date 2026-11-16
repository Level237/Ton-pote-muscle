@php
    use App\Support\MemberMeals;

    $today = $todayMeals ?? ['breakfast' => null, 'lunch' => null, 'dinner' => null, 'note' => null, 'filled' => 0];
    $plan = $mealPlan ?? null;
    $prescribedCount = $plan?->hasAny()
        ? collect(array_keys(MemberMeals::SLOTS))->filter(fn ($slot) => filled($plan->titleFor($slot)))->count()
        : 0;
    $filledPrescribed = collect(array_keys(MemberMeals::SLOTS))
        ->filter(fn ($slot) => filled($plan?->titleFor($slot)) && filled($today[$slot] ?? null))
        ->count();
    $dotClass = fn (?string $status) => match ($status) {
        'tenu' => 'bg-primary',
        'approx' => 'bg-primary/40',
        'ecart' => 'bg-danger',
        default => 'bg-border',
    };
    $chipClass = fn (bool $active, string $status) => $active
        ? match ($status) {
            'tenu' => 'bg-primary text-bg',
            'approx' => 'border border-primary bg-primary/15 text-primary',
            default => 'border border-danger/40 bg-danger/15 text-danger',
        }
        : 'border border-border text-muted hover:border-primary hover:text-primary';
    $weekEcarts = collect($weekMeals)->sum('ecarts');
@endphp

<section class="flex h-full flex-col rounded-xl border border-border bg-surface p-5">
    <p class="text-[11px] font-bold uppercase tracking-wider text-primary">Repas aujourd’hui</p>

    @if ($coach)
        <form method="POST" action="{{ route('admin.enrollments.meals.update', $member) }}" class="mt-4 space-y-3">
            @csrf
            @method('PUT')
            <p class="text-sm text-muted">Tu donnes les assiettes. Le membre indique s’il a tenu.</p>
            @foreach (MemberMeals::SLOTS as $slot => $label)
                <div>
                    <label for="meal_{{ $slot }}" class="text-xs font-semibold text-text">{{ $label }}</label>
                    <input
                        id="meal_{{ $slot }}"
                        name="{{ $slot }}"
                        type="text"
                        maxlength="140"
                        value="{{ old($slot, $plan?->{$slot}) }}"
                        placeholder="Ex. flocon, œufs…"
                        class="mt-1.5 min-h-12 w-full rounded-xl border border-border bg-bg px-3 text-sm text-text outline-none placeholder:text-muted/50 focus:border-primary focus:ring-2 focus:ring-primary"
                    >
                    @error($slot)
                        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach
            <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-primary px-4 text-sm font-semibold text-bg hover:bg-primary-hover">
                {{ $plan?->hasAny() ? 'Mettre à jour les repas' : 'Donner les repas' }}
            </button>
        </form>
    @elseif (! $plan?->hasAny())
        <p class="mt-3 text-sm text-muted">Ton coach ne t’a pas encore donné de repas.</p>
    @else
        <p class="mt-2 font-display text-3xl font-bold tabular-nums tracking-wide">
            {{ $filledPrescribed }}<span class="text-xl text-muted"> / {{ $prescribedCount }}</span>
        </p>
        <p class="mt-1 text-sm text-muted">
            Suivi des repas donnés par ton coach
            @if ($weekEcarts > 0)
                · {{ $weekEcarts }} écart{{ $weekEcarts > 1 ? 's' : '' }} cette semaine
            @endif
        </p>
    @endif

    @if ($plan?->hasAny())
        <div class="mt-5 space-y-4 {{ $coach ? 'border-t border-border pt-4' : '' }}">
            @foreach (MemberMeals::SLOTS as $slot => $label)
                @php $title = $plan->titleFor($slot); @endphp
                @if ($title)
                    <div>
                        <p class="text-xs font-semibold text-muted">{{ $label }}</p>
                        <p class="mt-0.5 text-sm font-medium text-text">{{ $title }}</p>
                        @if ($coach)
                            <p class="mt-1.5 text-sm {{ $today[$slot] ? 'text-text' : 'text-muted' }}">
                                {{ $today[$slot] ? MemberMeals::STATUSES[$today[$slot]] : 'Pas encore noté' }}
                            </p>
                        @else
                            <form method="POST" action="{{ route('member.meals.store') }}" class="mt-1.5 grid grid-cols-3 gap-1.5">
                                @csrf
                                <input type="hidden" name="meal" value="{{ $slot }}">
                                @foreach (MemberMeals::STATUSES as $value => $statusLabel)
                                    <button
                                        type="submit"
                                        name="status"
                                        value="{{ $value }}"
                                        class="inline-flex min-h-11 items-center justify-center rounded-xl px-1 text-center text-[11px] font-semibold leading-tight {{ $chipClass($today[$slot] === $value, $value) }}"
                                    >
                                        {{ $statusLabel }}
                                    </button>
                                @endforeach
                            </form>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>

        <p class="mt-5 text-[11px] font-bold uppercase tracking-wider text-muted">Cette semaine</p>
        <div class="mt-2 grid grid-cols-7 gap-1.5">
            @foreach ($weekMeals as $day)
                <div class="flex flex-col items-center gap-1.5">
                    <div class="flex flex-col items-center gap-1 rounded-lg bg-bg px-1 py-1.5 {{ $day['is_today'] ? 'ring-1 ring-primary' : '' }}">
                        @foreach (array_keys(MemberMeals::SLOTS) as $slot)
                            @if (filled($plan->titleFor($slot)))
                                <span class="h-1.5 w-1.5 rounded-full {{ $dotClass($day[$slot]) }}"></span>
                            @endif
                        @endforeach
                    </div>
                    <span class="text-[10px] {{ $day['is_today'] ? 'font-semibold text-primary' : 'text-muted' }}">{{ $day['letter'] }}</span>
                </div>
            @endforeach
        </div>

        @if ($coach)
            @if ($today['note'])
                <p class="mt-4 rounded-xl border border-border bg-bg px-3 py-2 text-sm text-text">{{ $today['note'] }}</p>
            @else
                <p class="mt-4 text-xs text-muted">Pas de note aujourd’hui.</p>
            @endif
        @else
            <form method="POST" action="{{ route('member.meals.note') }}" class="mt-5 border-t border-border pt-4">
                @csrf
                <label for="meal_note" class="text-xs font-medium text-muted">Note du jour (optionnel)</label>
                <div class="mt-2 flex gap-2">
                    <input
                        id="meal_note"
                        name="note"
                        type="text"
                        maxlength="140"
                        value="{{ old('note', $today['note']) }}"
                        placeholder="Ex. resto, trop salé…"
                        class="min-h-12 min-w-0 flex-1 rounded-xl border border-border bg-bg px-3 text-sm text-text outline-none placeholder:text-muted/50 focus:border-primary focus:ring-2 focus:ring-primary"
                    >
                    <button type="submit" class="inline-flex min-h-12 shrink-0 items-center justify-center rounded-xl border border-border px-4 text-sm font-semibold text-text hover:border-primary hover:text-primary">
                        Noter
                    </button>
                </div>
                @error('note')
                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </form>
            <p class="mt-3 text-xs text-muted">Retap sur le même état pour l’effacer.</p>
        @endif
    @endif
</section>
