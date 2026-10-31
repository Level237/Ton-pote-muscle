@props([
    'members',
])

<x-admin.dashboard.section-card
    title="Membres récents"
    :href="route('admin.enrollments.index')"
    empty-text="Aucun membre."
    :is-empty="$members->isEmpty()"
>
    <div class="divide-y divide-border px-5">
        @foreach ($members as $member)
            <div class="grid grid-cols-1 gap-1 py-3.5 sm:grid-cols-3 sm:items-center sm:gap-4">
                <p class="truncate text-sm font-medium text-text">
                    {{ $member->username ?? $member->name }}
                </p>
                <p class="truncate text-xs text-muted">
                    {{ $member->email }}
                </p>
                <p class="text-xs text-muted sm:text-right">
                    {{ $member->created_at->diffForHumans() }}
                </p>
            </div>
        @endforeach
    </div>
</x-admin.dashboard.section-card>
