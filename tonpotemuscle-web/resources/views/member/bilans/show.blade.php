@php
    use App\Enums\BilanQuestionType;

    $questions = $bilan->questions;
    $total = max(1, $questions->count());
    $start = 0;

    foreach ($questions as $index => $question) {
        if ($errors->has('answers.'.$question->id)) {
            $start = $index;
            break;
        }
    }

    $inputClass = 'min-h-12 w-full rounded-xl border border-border bg-surface px-4 text-base text-text outline-none placeholder:text-muted/50 focus:border-primary focus:ring-2 focus:ring-primary';
@endphp

<x-layouts.focus :title="$bilan->title">
    <div
        id="bilan-stepper"
        class="mx-auto flex min-h-dvh w-full max-w-lg flex-col px-5 py-5 sm:px-8"
        data-start="{{ $start }}"
    >
        <header>
            <div class="flex items-center justify-between gap-4">
                @if ($onboarding ?? false)
                    <img src="{{ asset('assets/img/logo.webp') }}" alt="Ton Pote Musclé" class="h-7 w-auto">
                @else
                    <a href="{{ route('member.bilans.index') }}" class="text-sm text-muted hover:text-text">← Bilans</a>
                @endif
                <p class="text-sm tabular-nums text-muted">
                    <span data-step-current>1</span> / {{ $total }}
                </p>
            </div>
            <div class="mt-4 h-1 overflow-hidden rounded-full bg-border">
                <div data-step-bar class="h-full rounded-full bg-primary transition-[width] duration-200" style="width: {{ round(100 / $total) }}%"></div>
            </div>
            <p class="mt-4 text-sm text-muted">{{ $bilan->title }}</p>
        </header>

        <form method="POST" action="{{ route('member.bilans.store', $bilan) }}" class="flex min-h-0 flex-1 flex-col">
            @csrf

            <div class="flex flex-1 flex-col justify-center py-8">
                @foreach ($questions as $index => $question)
                    @php
                        $name = 'answers.'.$question->id;
                        $value = old($name, $answers[$question->id] ?? '');
                    @endphp

                    <section
                        data-step="{{ $index }}"
                        class="{{ $index === $start ? '' : 'hidden' }}"
                    >
                        <h1 class="text-2xl font-semibold leading-snug text-text sm:text-3xl">{{ $question->title }}</h1>

                        @if ($question->type === BilanQuestionType::Text)
                            <textarea
                                id="answer-{{ $question->id }}"
                                name="answers[{{ $question->id }}]"
                                rows="4"
                                class="{{ $inputClass }} mt-8 py-3"
                            >{{ $value }}</textarea>
                        @elseif ($question->type === BilanQuestionType::Number)
                            <input
                                id="answer-{{ $question->id }}"
                                name="answers[{{ $question->id }}]"
                                type="number"
                                step="any"
                                inputmode="decimal"
                                value="{{ $value }}"
                                class="{{ $inputClass }} mt-8"
                            >
                        @elseif ($question->type === BilanQuestionType::Choice)
                            <div class="mt-8 space-y-2">
                                @foreach ($question->options ?? [] as $option)
                                    <label class="block cursor-pointer">
                                        <input
                                            type="radio"
                                            name="answers[{{ $question->id }}]"
                                            value="{{ $option }}"
                                            class="peer sr-only"
                                            data-step-choice
                                            @checked((string) $value === (string) $option)
                                        >
                                        <span class="flex min-h-12 items-center rounded-xl border border-border px-4 text-sm text-text transition-colors peer-checked:border-primary peer-checked:bg-primary/10">
                                            {{ $option }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        @elseif ($question->type === BilanQuestionType::Rating)
                            <div class="mt-8 flex gap-2">
                                @foreach (range(1, 5) as $score)
                                    <label class="min-w-0 flex-1 cursor-pointer">
                                        <input
                                            type="radio"
                                            name="answers[{{ $question->id }}]"
                                            value="{{ $score }}"
                                            class="peer sr-only"
                                            data-step-choice
                                            @checked((string) $value === (string) $score)
                                        >
                                        <span class="flex h-12 items-center justify-center rounded-xl border border-border text-sm font-medium text-muted transition-colors peer-checked:border-primary peer-checked:bg-primary peer-checked:text-bg">
                                            {{ $score }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        @error($name)
                            <p class="mt-3 text-sm text-danger">{{ $message }}</p>
                        @enderror
                    </section>
                @endforeach
            </div>

            @error('answers')
                <p class="mb-3 text-sm text-danger">{{ $message }}</p>
            @enderror

            <div class="grid grid-cols-2 gap-3 pb-[max(1rem,env(safe-area-inset-bottom))]">
                <button
                    type="button"
                    data-step-prev
                    class="inline-flex min-h-12 items-center justify-center rounded-xl border border-border bg-surface px-4 text-sm font-medium text-text disabled:opacity-30"
                >
                    Retour
                </button>
                <button
                    type="button"
                    data-step-next
                    class="inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-primary px-4 text-sm font-bold text-bg"
                >
                    Suivant
                </button>
            </div>
        </form>
    </div>
</x-layouts.focus>
