@props([
    'members',
    'search' => '',
    'unreadOnly' => false,
    'activeMember' => null,
    'formAction' => null,
])

@php
    $action = $formAction ?? route('admin.messages.index');
    $queryExtra = array_filter([
        'q' => $search ?: null,
        'unread' => $unreadOnly ? 1 : null,
    ]);
@endphp

<div class="flex h-full min-h-0 flex-col bg-surface">
    <div class="space-y-3 border-b border-border p-3.5">
        <div class="flex items-center justify-between gap-2">
            <h1 class="font-display text-xl font-bold uppercase tracking-wide text-text">Messages</h1>
            <div class="flex rounded-lg border border-border bg-bg p-0.5 text-xs font-medium">
                <a
                    href="{{ route('admin.messages.index', array_filter(['q' => $search ?: null])) }}"
                    class="rounded-md px-2.5 py-1 transition-colors {{ ! $unreadOnly ? 'bg-primary/15 text-primary font-semibold' : 'text-muted hover:text-text' }}"
                >
                    Tous
                </a>
                <a
                    href="{{ route('admin.messages.index', array_filter(['unread' => 1, 'q' => $search ?: null])) }}"
                    class="rounded-md px-2.5 py-1 transition-colors {{ $unreadOnly ? 'bg-primary/15 text-primary font-semibold' : 'text-muted hover:text-text' }}"
                >
                    Non lus
                </a>
            </div>
        </div>

        <form method="GET" action="{{ $action }}" class="relative">
            @if ($unreadOnly)
                <input type="hidden" name="unread" value="1">
            @endif
            <label class="sr-only" for="chat-q">Rechercher</label>
            <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-muted"></i>
            <input
                id="chat-q"
                name="q"
                type="search"
                value="{{ $search }}"
                placeholder="Rechercher un membre…"
                class="h-10 w-full rounded-xl border border-border bg-bg pl-8 pr-3 text-sm text-text outline-none transition-colors placeholder:text-muted/60 focus:border-primary"
            >
        </form>
    </div>

    @if ($members->isEmpty())
        <div class="flex flex-1 flex-col items-center justify-center px-6 py-12 text-center">
            <div class="flex h-11 w-11 items-center justify-center rounded-full border border-border bg-bg text-muted">
                <i class="fa-regular fa-comment-dots text-base"></i>
            </div>
            <p class="mt-3 text-sm font-medium text-text">
                {{ $unreadOnly ? 'Aucun message non lu' : 'Aucun membre trouvé' }}
            </p>
            <p class="mt-1 text-xs text-muted">
                {{ $unreadOnly ? 'Tu es à jour sur tous les suivis.' : 'Essaie un autre nom ou pseudo.' }}
            </p>
        </div>
    @else
        <ul class="min-h-0 flex-1 divide-y divide-border/70 overflow-y-auto">
            @foreach ($members as $item)
                @php
                    $last = $item->latestCoachMessage;
                    $unread = (int) $item->unread_count;
                    $isActive = $activeMember && $activeMember->id === $item->id;
                    $stamp = null;
                    if ($last?->created_at) {
                        $stamp = $last->created_at->isToday()
                            ? $last->created_at->format('H:i')
                            : ($last->created_at->isYesterday() ? 'Hier' : $last->created_at->format('d/m'));
                    }
                @endphp
                <li>
                    <a
                        href="{{ route('admin.messages.show', array_merge(['user' => $item], $queryExtra)) }}"
                        @if ($isActive) aria-current="page" @endif
                        class="flex items-center gap-3 px-3.5 py-3 transition-colors duration-150 {{ $isActive ? 'bg-primary/[0.08]' : 'hover:bg-white/[0.03]' }}"
                    >
                        <div class="relative flex h-11 w-11 shrink-0 items-center justify-center rounded-full font-display text-sm font-bold uppercase {{ $isActive ? 'bg-primary text-bg' : 'border border-border bg-bg text-text' }}">
                            {{ mb_substr($item->name ?: $item->username, 0, 1) }}
                            @if ($unread > 0 && ! $isActive)
                                <span class="absolute -right-0.5 -top-0.5 h-2.5 w-2.5 rounded-full bg-primary ring-2 ring-surface"></span>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-baseline justify-between gap-2">
                                <p class="truncate text-sm {{ $unread > 0 ? 'font-semibold text-text' : 'font-medium text-text' }}">
                                    {{ $item->name }}
                                </p>
                                @if ($stamp)
                                    <time
                                        class="shrink-0 text-[11px] tabular-nums {{ $unread > 0 ? 'font-semibold text-primary' : 'text-muted' }}"
                                        datetime="{{ $last->created_at->toIso8601String() }}"
                                    >
                                        {{ $stamp }}
                                    </time>
                                @endif
                            </div>

                            <div class="mt-0.5 flex items-center justify-between gap-2">
                                <p class="truncate text-xs {{ $unread > 0 ? 'font-medium text-text/90' : 'text-muted' }}">
                                    @if ($last)
                                        @if (! $last->from_member)
                                            <span class="text-muted">Toi :</span>
                                        @endif
                                        {{ \Illuminate\Support\Str::limit($last->body, 48) }}
                                    @else
                                        <span class="text-muted/75">Lancer la discussion</span>
                                    @endif
                                </p>
                                <x-chat.badge :count="$unread" />
                            </div>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>

        @if ($members->hasPages())
            <div class="border-t border-border px-3 py-2">
                {{ $members->links() }}
            </div>
        @endif
    @endif
</div>
