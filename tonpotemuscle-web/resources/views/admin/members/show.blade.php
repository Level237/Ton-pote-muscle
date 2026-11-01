<x-layouts.admin :title="$member->name">
    <div class="mx-auto max-w-xl">
        @include('training.board', [
            'coach' => true,
            'tabRoute' => 'admin.enrollments.show',
            'tabParams' => ['user' => $member],
        ])
    </div>
</x-layouts.admin>
