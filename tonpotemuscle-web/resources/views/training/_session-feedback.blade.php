@if ($coach && ($log ?? null))
    @if ($log->feedbackLabel())
        <p class="{{ $class ?? 'mt-3 text-sm text-text' }}">
            Ressenti · <span class="font-semibold">{{ $log->feedbackLabel() }}</span>
        </p>
    @else
        <p class="{{ $class ?? 'mt-3 text-sm text-muted' }}">Ressenti non noté</p>
    @endif
@endif
