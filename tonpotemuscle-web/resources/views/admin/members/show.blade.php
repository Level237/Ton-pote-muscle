<x-layouts.admin :title="$member->name">
    <div class="mx-auto max-w-xl space-y-8">
        @include('training.board', [
            'coach' => true,
            'tabRoute' => 'admin.enrollments.show',
            'tabParams' => ['user' => $member],
        ])

        @include('admin.members._bilans', ['member' => $member])
    </div>
</x-layouts.admin>
