@php
    use App\Support\MemberWeight;

    $defaultWeight = old('weight_kg', $weekWeight?->weight_kg ?? $member->weight_kg);
@endphp

<section class="rounded-xl border border-border bg-surface p-5">
    <p class="text-[11px] font-bold uppercase tracking-wider text-primary">Pesée de la semaine</p>

    @if ($weekWeight)
        <p class="mt-2 font-display text-3xl font-bold tabular-nums tracking-wide">{{ $weekWeight->formatted() }}</p>
        <p class="mt-1 text-sm text-muted">Noté {{ $weekWeight->weighed_on->format('d/m') }}</p>
    @elseif ($member->weight_kg)
        <p class="mt-2 text-sm text-muted">Dernier poids connu · {{ MemberWeight::format($member->weight_kg) }}</p>
    @else
        <p class="mt-2 text-sm text-muted">{{ $coach ? 'Pas encore de pesée.' : 'Une fois par semaine, ça suffit.' }}</p>
    @endif

    @if (! $coach)
        <form method="POST" action="{{ route('member.weight.store') }}" class="mt-4 flex gap-2">
            @csrf
            <label class="sr-only" for="weight_kg">Poids en kg</label>
            <input
                id="weight_kg"
                name="weight_kg"
                type="number"
                inputmode="decimal"
                step="0.1"
                min="30"
                max="300"
                value="{{ $defaultWeight }}"
                required
                placeholder="kg"
                class="min-h-12 min-w-0 flex-1 rounded-xl border border-border bg-bg px-3 text-sm text-text outline-none placeholder:text-muted/50 focus:border-primary focus:ring-2 focus:ring-primary"
            >
            <button
                type="submit"
                class="inline-flex min-h-12 shrink-0 items-center justify-center rounded-xl bg-primary px-4 text-sm font-semibold text-bg hover:bg-primary-hover"
            >
                {{ $weekWeight ? 'Mettre à jour' : 'Enregistrer' }}
            </button>
        </form>
        @error('weight_kg')
            <p class="mt-2 text-sm text-danger">{{ $message }}</p>
        @enderror
    @endif

    @if ($weightLogs->isNotEmpty())
        <ul class="mt-4 divide-y divide-border border-t border-border text-sm">
            @foreach ($weightLogs->take(4) as $log)
                <li class="flex items-center justify-between py-2">
                    <span class="text-muted">{{ $log->week_start->format('d/m') }}</span>
                    <span class="tabular-nums text-text">{{ $log->formatted() }}</span>
                </li>
            @endforeach
        </ul>
    @endif
</section>
