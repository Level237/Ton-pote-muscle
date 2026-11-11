<x-layouts.focus :title="'C’est fait.'">
    <div class="mx-auto flex min-h-dvh w-full max-w-sm flex-col px-4 py-5 sm:py-8">
        <header class="flex items-center gap-3">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary text-bg" aria-hidden="true">
                <i class="fa-solid fa-check text-sm"></i>
            </span>
            <div class="min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-primary">C’est fait</p>
                <h1 class="font-display text-2xl font-bold uppercase leading-none tracking-wide">Séance terminée</h1>
            </div>
        </header>

        <div class="mt-5">
            <x-share.card-workout :log="$log" />
        </div>

        <section class="mt-6">
            <h2 class="text-sm font-semibold text-text">Comment tu as trouvé cette séance ?</h2>
            <form method="POST" action="{{ route('member.workouts.feedback', $item) }}" class="mt-3 grid grid-cols-3 gap-2">
                @csrf
                @foreach ($feedbacks as $value => $label)
                    <button
                        type="submit"
                        name="difficulty_feedback"
                        value="{{ $value }}"
                        class="inline-flex min-h-14 items-center justify-center rounded-xl px-2 text-center text-xs font-semibold leading-tight transition-transform duration-150 ease-out active:scale-[0.97] motion-reduce:transform-none {{ (int) $log->difficulty_feedback === (int) $value ? 'bg-primary text-bg' : 'border border-border text-text hover:border-primary hover:text-primary' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </form>
            @if ($log->feedbackLabel())
                <p class="mt-2 text-sm text-muted">Noté · {{ $log->feedbackLabel() }}</p>
            @endif
        </section>

        <div class="mt-auto pt-8">
            <x-share.actions :url="$shareUrl" :title="$shareTitle" :text="$shareText" />
            <a href="{{ route('dashboard') }}" class="mt-4 mb-2 inline-flex min-h-11 w-full items-center justify-center text-sm font-medium text-muted hover:text-text">
                Retour à l’accueil
            </a>
        </div>
    </div>
</x-layouts.focus>
