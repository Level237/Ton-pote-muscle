<x-layouts.member title="Tableau de bord">
    @include('training.board', [
        'coach' => false,
        'tabRoute' => 'dashboard',
        'tabParams' => [],
    ])
</x-layouts.member>
