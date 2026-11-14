@php
    $subtitleParts = array_filter([
        $member->username ? '@'.$member->username : null,
        'Membre',
    ]);
    $listQuery = array_filter([
        'q' => $search ?: null,
        'unread' => $unreadOnly ? 1 : null,
    ]);
@endphp

<x-layouts.admin :title="'Message · '.$member->name">
    <div class="overflow-hidden rounded-2xl border border-border bg-surface lg:grid lg:h-[calc(100dvh-8.5rem)] lg:min-h-[34rem] lg:grid-cols-[20rem_1fr]">
        <aside class="hidden min-h-0 lg:block lg:h-full lg:border-r lg:border-border">
            <x-chat.inbox-list
                :members="$members"
                :search="$search"
                :unread-only="$unreadOnly"
                :active-member="$member"
                :form-action="route('admin.messages.show', $member)"
            />
        </aside>

        <section class="h-[calc(100dvh-10.5rem)] min-h-[26rem] lg:h-full">
            <x-chat.thread
                :messages="$messages"
                :action="route('admin.messages.store', $member)"
                :peer-name="$member->name"
                :peer-subtitle="implode(' · ', $subtitleParts)"
                :back-url="route('admin.messages.index', $listQuery)"
                :profile-url="route('admin.enrollments.show', $member)"
                :refresh-url="route('admin.messages.show', array_merge(['user' => $member], $listQuery))"
            />
        </section>
    </div>
</x-layouts.admin>
