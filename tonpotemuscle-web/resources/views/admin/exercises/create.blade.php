<x-layouts.admin title="Nouvel exercice">
    <div class="space-y-8">
        <header>
            <h1 class="font-display text-2xl font-bold uppercase tracking-wide">Nouvel exercice</h1>
        </header>

        <form method="POST" action="{{ route('admin.exercises.store') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @include('admin.exercises._form')

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="inline-flex min-h-11 items-center rounded-lg bg-primary px-4 text-sm font-semibold text-bg hover:bg-primary-hover">
                    Créer
                </button>
                <a href="{{ route('admin.exercises.index') }}" class="inline-flex min-h-11 items-center rounded-lg border border-border px-4 text-sm font-medium text-text hover:border-primary hover:text-primary">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</x-layouts.admin>
