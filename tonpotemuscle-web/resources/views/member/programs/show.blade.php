<x-layouts.member :title="$program->title">
    <div class="space-y-6">
        <div>
            <a href="{{ route('member.programs.index') }}" class="text-sm font-medium text-muted hover:text-primary">
                ← Mes programmes
            </a>
            <h1 class="mt-3 font-display text-2xl font-bold uppercase tracking-wide">{{ $program->title }}</h1>
            @if ($program->description)
                <p class="mt-2 text-sm text-muted">{{ $program->description }}</p>
            @endif
        </div>

        @if ($assignments->isEmpty())
            <p class="rounded-xl border border-border bg-surface px-5 py-8 text-sm text-muted">
                Aucune semaine n’a encore été placée sur ton calendrier.
            </p>
        @else
            <section class="overflow-hidden rounded-xl border border-border bg-surface">
                <h2 class="border-b border-border px-4 py-3 text-sm font-medium text-text">Semaines données</h2>
                <ul class="divide-y divide-border">
                    @foreach ($assignments as $assignment)
                        <li class="px-4 py-3.5">
                            <p class="text-sm text-text">Semaine {{ $assignment->program_week }}</p>
                            <p class="mt-0.5 text-xs text-muted">
                                {{ $assignment->starts_on->translatedFormat('d M') }}
                                @if ($assignment->ends_on)
                                    — {{ $assignment->ends_on->translatedFormat('d M Y') }}
                                @endif
                            </p>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    </div>
</x-layouts.member>
