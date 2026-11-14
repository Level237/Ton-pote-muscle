<x-layouts.admin :title="$member->name">
    <div class="mx-auto max-w-xl space-y-8">
        <div class="flex justify-end">
            <a href="{{ route('admin.messages.show', $member) }}" class="min-h-11 text-sm font-medium text-text hover:text-primary">
                Écrire un message
            </a>
        </div>
        @include('training.board', [
            'coach' => true,
            'tabRoute' => 'admin.enrollments.show',
            'tabParams' => ['user' => $member],
        ])

        @include('admin.members._bilans', ['member' => $member])
    </div>
</x-layouts.admin>
