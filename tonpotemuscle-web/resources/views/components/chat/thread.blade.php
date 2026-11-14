@props([
    'messages',
    'action',
    'peerName' => 'Discussion',
    'peerSubtitle' => null,
    'peerInitial' => null,
    'backUrl' => null,
    'profileUrl' => null,
    'refreshUrl' => null,
])

@php
    $initial = strtoupper(mb_substr($peerInitial ?: $peerName, 0, 1));
    $viewerId = auth()->id();
@endphp

<div class="flex h-full min-h-0 flex-col bg-bg">
    <header class="flex h-15 shrink-0 items-center justify-between gap-3 border-b border-border bg-surface px-3.5 sm:px-4">
        <div class="flex min-w-0 items-center gap-3">
            @if ($backUrl)
                <a
                    href="{{ $backUrl }}"
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-muted transition-colors hover:bg-white/[0.05] hover:text-text lg:hidden"
                    aria-label="Retour aux discussions"
                >
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
            @endif

            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-primary/30 bg-primary/15 font-display text-sm font-bold uppercase text-primary">
                {{ $initial }}
            </div>

            <div class="min-w-0">
                <h2 class="truncate text-sm font-semibold text-text sm:text-base">{{ $peerName }}</h2>
                @if ($peerSubtitle)
                    <p class="truncate text-xs text-muted">{{ $peerSubtitle }}</p>
                @endif
            </div>
        </div>

        <div class="flex shrink-0 items-center gap-1.5">
            @if ($refreshUrl)
                <a
                    href="{{ $refreshUrl }}"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-border bg-bg text-xs text-muted transition-colors hover:border-primary/40 hover:text-text"
                    title="Actualiser les messages"
                    aria-label="Actualiser les messages"
                >
                    <i class="fa-solid fa-rotate-right"></i>
                </a>
            @endif
            @if ($profileUrl)
                <a
                    href="{{ $profileUrl }}"
                    class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-border bg-bg px-3 text-xs font-medium text-text transition-colors hover:border-primary hover:text-primary"
                >
                    <span>Fiche</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-muted"></i>
                </a>
            @endif
        </div>
    </header>

    <div data-chat-scroll class="min-h-0 flex-1 overflow-y-auto px-3.5 py-4 sm:px-5">
        @if ($messages->isEmpty())
            <div class="flex h-full min-h-60 flex-col items-center justify-center px-4 text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-border bg-surface text-primary">
                    <i class="fa-regular fa-paper-plane text-base"></i>
                </div>
                <p class="mt-3 text-sm font-semibold text-text">Aucun message pour l’instant</p>
                <p class="mt-1 max-w-xs text-xs text-muted">
                    Pas encore de message. Écris ci-dessous pour lancer la discussion avec {{ $peerName }}.
                </p>
            </div>
        @else
            <div class="space-y-1">
                @php
                    $previousDay = null;
                    $total = $messages->count();
                @endphp

                @foreach ($messages as $idx => $message)
                    @php
                        $mine = $message->author_id === $viewerId;
                        $created = $message->created_at;
                        $dayKey = $created?->toDateString();
                        $showDay = $dayKey !== $previousDay;
                        $previousDay = $dayKey;

                        $prev = $idx > 0 ? $messages[$idx - 1] : null;
                        $next = $idx < $total - 1 ? $messages[$idx + 1] : null;

                        $sameAuthorAsPrev = ! $showDay
                            && $prev
                            && $prev->author_id === $message->author_id
                            && $prev->created_at
                            && $created
                            && $prev->created_at->diffInMinutes($created) <= 10;

                        $sameAuthorAsNext = $next
                            && $next->author_id === $message->author_id
                            && $next->created_at
                            && $created
                            && $next->created_at->toDateString() === $dayKey
                            && $created->diffInMinutes($next->created_at) <= 10;

                        $dayLabel = null;
                        if ($showDay && $created) {
                            $dayLabel = $created->isToday()
                                ? "Aujourd'hui"
                                : ($created->isYesterday()
                                    ? 'Hier'
                                    : $created->locale('fr')->isoFormat('D MMM YYYY'));
                        }

                        $bubbleRadius = $mine
                            ? 'rounded-2xl '.($sameAuthorAsPrev ? 'rounded-tr-md ' : '').(! $sameAuthorAsNext ? 'rounded-br-sm' : 'rounded-br-md')
                            : 'rounded-2xl '.($sameAuthorAsPrev ? 'rounded-tl-md ' : '').(! $sameAuthorAsNext ? 'rounded-bl-sm' : 'rounded-bl-md');
                    @endphp

                    @if ($dayLabel)
                        <div class="{{ $idx > 0 ? 'pt-4' : '' }} pb-2 text-center">
                            <span class="inline-block rounded-full border border-border bg-surface px-3 py-0.5 text-[11px] font-medium text-muted">
                                {{ $dayLabel }}
                            </span>
                        </div>
                    @endif

                    <article class="flex {{ $mine ? 'justify-end' : 'justify-start' }} {{ $sameAuthorAsPrev ? 'pt-0.5' : 'pt-2.5' }}">
                        @if (! $mine)
                            <div class="mr-2 flex w-7 shrink-0 flex-col justify-end">
                                @if (! $sameAuthorAsNext)
                                    <span class="flex h-7 w-7 items-center justify-center rounded-full border border-border bg-surface font-display text-[11px] font-bold uppercase text-muted">
                                        {{ strtoupper(mb_substr($message->author?->name ?? $peerName, 0, 1)) }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        <div class="max-w-[82%] sm:max-w-[72%]">
                            @if (! $mine && ! $sameAuthorAsPrev)
                                <span class="sr-only">{{ $message->author?->name ?? $peerName }}</span>
                            @endif

                            <div class="px-3.5 py-2 text-sm leading-relaxed {{ $bubbleRadius }} {{ $mine ? 'bg-primary text-bg font-medium' : 'border border-border bg-surface text-text' }}">
                                <p class="whitespace-pre-wrap break-words">{{ $message->body }}</p>
                            </div>

                            @if (! $sameAuthorAsNext)
                                <p class="mt-1 flex items-center gap-1.5 px-1 text-[11px] tabular-nums text-muted {{ $mine ? 'justify-end' : 'justify-start' }}">
                                    <span>{{ $created?->format('H:i') }}</span>
                                    @if ($mine)
                                        @if ($message->read_at)
                                            <span class="inline-flex items-center gap-0.5 text-primary" title="Lu le {{ $message->read_at->format('d/m à H:i') }}">
                                                <i class="fa-solid fa-check-double text-[10px]"></i>
                                                <span>Lu</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-0.5 text-muted/80" title="Envoyé">
                                                <i class="fa-solid fa-check text-[10px]"></i>
                                            </span>
                                        @endif
                                    @endif
                                </p>
                            @endif
                        </div>
                    </article>
                @endforeach

                <div id="dernier"></div>
            </div>
        @endif
    </div>

    <form method="POST" action="{{ $action }}" data-chat-form class="shrink-0 border-t border-border bg-surface p-2.5 sm:p-3">
        @csrf
        @error('body')
            <p class="mb-2 px-1 text-xs text-danger">{{ $message }}</p>
        @enderror

        <div class="flex items-end gap-2 rounded-2xl border border-border bg-bg px-3 py-1.5 transition-colors focus-within:border-primary">
            <label class="sr-only" for="body">Message</label>
            <textarea
                id="body"
                name="body"
                rows="1"
                maxlength="2000"
                required
                placeholder="Écrire un message…"
                data-chat-input
                class="max-h-36 min-h-9 flex-1 resize-none bg-transparent py-1.5 text-sm leading-5 text-text outline-none placeholder:text-muted/60"
            >{{ old('body') }}</textarea>

            <button
                type="submit"
                aria-label="Envoyer"
                class="mb-0.5 inline-flex h-9 shrink-0 items-center justify-center gap-1.5 rounded-xl bg-primary px-3.5 text-xs font-bold text-bg transition-colors hover:bg-primary-hover"
            >
                <span class="hidden sm:inline">Envoyer</span>
                <i class="fa-solid fa-paper-plane text-xs"></i>
            </button>
        </div>

        <p class="mt-1.5 hidden px-1 text-[11px] text-muted/70 sm:block">
            Entrée pour envoyer · Maj + Entrée pour aller à la ligne
        </p>
    </form>
</div>

<script>
(() => {
    const scroller = document.querySelector('[data-chat-scroll]');
    if (scroller) scroller.scrollTop = scroller.scrollHeight;
    const input = document.querySelector('[data-chat-input]');
    const form = document.querySelector('[data-chat-form]');
    if (!input || !form) return;
    const fit = () => {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 144) + 'px';
    };
    fit();
    input.addEventListener('input', fit);
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) {
            e.preventDefault();
            if (input.value.trim() !== '') form.requestSubmit();
        }
    });
})();
</script>
