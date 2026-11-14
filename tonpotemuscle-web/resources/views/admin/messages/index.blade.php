<x-layouts.admin title="Messages">
    <div class="overflow-hidden rounded-2xl border border-border bg-surface lg:grid lg:h-[calc(100dvh-8.5rem)] lg:min-h-[34rem] lg:grid-cols-[20rem_1fr]">
        <aside class="h-[calc(100dvh-11rem)] min-h-[26rem] lg:h-full lg:border-r lg:border-border">
            <x-chat.inbox-list
                :members="$members"
                :search="$search"
                :unread-only="$unreadOnly"
            />
        </aside>

        <section class="hidden flex-col items-center justify-center bg-bg px-8 text-center lg:flex">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl border border-border bg-surface text-primary">
                <i class="fa-regular fa-comments text-xl"></i>
            </div>
            <h2 class="mt-4 font-display text-xl font-bold uppercase tracking-wide text-text">
                Suivi des membres
            </h2>
            <p class="mt-1.5 max-w-sm text-sm text-muted">
                Sélectionne un membre à gauche pour lire vos échanges ou lancer une nouvelle discussion.
            </p>
        </section>
    </div>
</x-layouts.admin>
