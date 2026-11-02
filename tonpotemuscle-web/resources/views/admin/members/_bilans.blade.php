<section class="overflow-hidden rounded-xl border border-border bg-surface">
    <div class="border-b border-border px-5 py-4">
        <h2 class="text-sm font-semibold text-text">Bilans</h2>
    </div>

    @if ($member->bilanSubmissions->isEmpty())
        <p class="px-5 py-8 text-sm text-muted">Aucun bilan renseigné.</p>
    @else
        <div class="divide-y divide-border">
            @foreach ($member->bilanSubmissions->sortByDesc('submitted_at') as $submission)
                <article class="px-5 py-4">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="text-sm font-medium text-text">{{ $submission->bilan?->title ?? 'Bilan' }}</h3>
                        <p class="shrink-0 text-xs text-muted">{{ $submission->submitted_at?->format('d/m/Y') }}</p>
                    </div>
                    <dl class="mt-3 space-y-3">
                        @foreach ($submission->answers as $answer)
                            <div>
                                <dt class="text-xs text-muted">{{ $answer->question?->title }}</dt>
                                <dd class="mt-0.5 text-sm text-text">{{ $answer->value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </article>
            @endforeach
        </div>
    @endif
</section>
