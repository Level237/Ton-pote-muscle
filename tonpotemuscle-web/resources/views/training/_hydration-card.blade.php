@php
    use App\Support\MemberHydration;

    $goal = $hydrationGoal ?? MemberHydration::DEFAULT_GOAL;
    $today = min($todayHydration ?? 0, MemberHydration::MAX_GLASSES);
    $reached = $today >= $goal;
    $percent = (int) round(min(100, $goal > 0 ? ($today / $goal) * 100 : 0));
@endphp

<section class="flex h-full flex-col rounded-xl border border-border bg-surface p-5">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-wider text-primary">Eau aujourd’hui</p>
            <p class="mt-2 font-display text-3xl font-bold tabular-nums tracking-wide">
                {{ $today }}<span class="text-xl text-muted"> / {{ $goal }}</span>
            </p>
            <p class="mt-1 text-sm text-muted">
                {{ MemberHydration::formatLiters($today) }}
                · objectif {{ MemberHydration::formatLiters($goal) }}
                @if ($reached)
                    · atteint
                @endif
            </p>
        </div>
        @if (! $coach)
            <div class="flex shrink-0 gap-2">
                <form method="POST" action="{{ route('member.hydration.store') }}">
                    @csrf
                    <input type="hidden" name="delta" value="-1">
                    <button
                        type="submit"
                        class="inline-flex h-12 w-12 items-center justify-center rounded-xl border border-border text-lg font-semibold text-text hover:border-primary hover:text-primary disabled:opacity-40"
                        @disabled($today <= 0)
                        aria-label="Retirer un verre"
                    >
                        −
                    </button>
                </form>
                <form method="POST" action="{{ route('member.hydration.store') }}">
                    @csrf
                    <input type="hidden" name="delta" value="1">
                    <button
                        type="submit"
                        class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-primary text-lg font-bold text-bg hover:bg-primary-hover disabled:opacity-40"
                        @disabled($today >= MemberHydration::MAX_GLASSES)
                        aria-label="Ajouter un verre"
                    >
                        +
                    </button>
                </form>
            </div>
        @endif
    </div>

    <div class="mt-4 h-2 overflow-hidden rounded-full bg-bg" role="progressbar" aria-valuemin="0" aria-valuemax="{{ $goal }}" aria-valuenow="{{ $today }}">
        <div class="h-full rounded-full {{ $reached ? 'bg-primary' : 'bg-primary/70' }}" style="width: {{ $percent }}%"></div>
    </div>

    <p class="mt-4 text-[11px] font-bold uppercase tracking-wider text-muted">Cette semaine</p>
    <div class="mt-2 flex h-16 items-end gap-1.5">
        @foreach ($weekHydration as $day)
            @php
                $fill = $day['goal'] > 0 ? min(100, (int) round(($day['glasses'] / $day['goal']) * 100)) : 0;
            @endphp
            <div class="flex min-w-0 flex-1 flex-col items-center justify-end gap-1.5">
                <div class="flex h-10 w-full items-end rounded-sm bg-bg">
                    <div
                        class="w-full rounded-sm {{ $day['glasses'] >= $day['goal'] && $day['glasses'] > 0 ? 'bg-primary' : ($day['is_today'] ? 'bg-primary/70' : 'bg-border') }}"
                        style="height: {{ $day['glasses'] === 0 ? 3 : max(6, (int) round(($fill / 100) * 40)) }}px"
                    ></div>
                </div>
                <span class="text-[10px] {{ $day['is_today'] ? 'font-semibold text-primary' : 'text-muted' }}">{{ $day['letter'] }}</span>
            </div>
        @endforeach
    </div>

    @if ($coach)
        <form method="POST" action="{{ route('admin.enrollments.hydration.update', $member) }}" class="mt-5 border-t border-border pt-4">
            @csrf
            @method('PUT')
            <label for="hydration_goal_glasses" class="text-xs font-medium text-muted">Objectif quotidien (verres de 25 cl)</label>
            <div class="mt-2 flex gap-2">
                <input
                    id="hydration_goal_glasses"
                    name="hydration_goal_glasses"
                    type="number"
                    min="{{ MemberHydration::MIN_GOAL }}"
                    max="{{ MemberHydration::MAX_GOAL }}"
                    value="{{ old('hydration_goal_glasses', $goal) }}"
                    required
                    class="min-h-12 min-w-0 flex-1 rounded-xl border border-border bg-bg px-3 text-sm text-text outline-none focus:border-primary focus:ring-2 focus:ring-primary"
                >
                <button type="submit" class="inline-flex min-h-12 shrink-0 items-center justify-center rounded-xl bg-primary px-4 text-sm font-semibold text-bg hover:bg-primary-hover">
                    Appliquer
                </button>
            </div>
            @error('hydration_goal_glasses')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </form>
    @else
        <p class="mt-4 text-xs text-muted">Un verre = 25 cl. Ton coach fixe l’objectif.</p>
    @endif
</section>
