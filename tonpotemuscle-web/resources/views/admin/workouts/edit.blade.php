<x-layouts.admin title="{{ $workout->title }}">
    <div class="space-y-8">
        <header>
            <h1 class="font-display text-2xl font-bold uppercase tracking-wide">{{ $workout->title }}</h1>
        </header>

        <form method="POST" action="{{ route('admin.workouts.update', $workout) }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')
            @include('admin.workouts._form')

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="inline-flex min-h-11 items-center rounded-lg bg-primary px-4 text-sm font-semibold text-bg hover:bg-primary-hover">
                    Enregistrer
                </button>
                <a href="{{ route('admin.workouts.index') }}" class="inline-flex min-h-11 items-center rounded-lg border border-border px-4 text-sm font-medium text-text hover:border-primary hover:text-primary">
                    Retour
                </a>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.workouts.destroy', $workout) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-sm font-medium text-muted hover:text-danger">
                Archiver cette séance
            </button>
        </form>
    </div>
</x-layouts.admin>
