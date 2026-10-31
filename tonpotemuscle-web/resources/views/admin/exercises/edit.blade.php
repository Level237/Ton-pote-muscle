<x-layouts.admin title="{{ $exercise->title }}">
    <div class="space-y-8">
        <header>
            <h1 class="font-display text-2xl font-bold uppercase tracking-wide">{{ $exercise->title }}</h1>
        </header>

        <form method="POST" action="{{ route('admin.exercises.update', $exercise) }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')
            @include('admin.exercises._form')

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="inline-flex min-h-11 items-center rounded-lg bg-primary px-4 text-sm font-semibold text-bg hover:bg-primary-hover">
                    Enregistrer
                </button>
                <a href="{{ route('admin.exercises.index') }}" class="inline-flex min-h-11 items-center rounded-lg border border-border px-4 text-sm font-medium text-text hover:border-primary hover:text-primary">
                    Retour
                </a>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.exercises.destroy', $exercise) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-sm font-medium text-muted hover:text-danger">
                Archiver cet exercice
            </button>
        </form>
    </div>
</x-layouts.admin>
