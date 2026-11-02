@php
    use App\Enums\BilanQuestionType;
    $inputClass = 'min-h-11 w-full rounded-lg border border-border bg-bg px-3 text-sm text-text outline-none placeholder:text-muted/50 focus:border-primary focus:ring-2 focus:ring-primary';
@endphp

<x-layouts.member :title="$bilan->title">
    <div class="space-y-6">
        <div>
            <a href="{{ route('member.bilans.index') }}" class="text-sm font-medium text-muted hover:text-primary">
                ← Bilans
            </a>
            <h1 class="mt-3 font-display text-2xl font-bold uppercase tracking-wide">{{ $bilan->title }}</h1>
            @if ($bilan->description)
                <p class="mt-2 text-sm text-muted">{{ $bilan->description }}</p>
            @endif
        </div>

        <form method="POST" action="{{ route('member.bilans.store', $bilan) }}" class="space-y-6">
            @csrf

            @foreach ($bilan->questions as $question)
                @php
                    $name = 'answers.'.$question->id;
                    $value = old($name, $answers[$question->id] ?? '');
                @endphp

                <fieldset class="rounded-xl border border-border bg-surface p-4">
                    <legend class="px-1 text-sm font-medium text-text">{{ $question->title }}</legend>

                    @if ($question->type === BilanQuestionType::Text)
                        <textarea
                            id="answer-{{ $question->id }}"
                            name="answers[{{ $question->id }}]"
                            rows="3"
                            class="{{ $inputClass }} mt-3 py-2.5"
                        >{{ $value }}</textarea>
                    @elseif ($question->type === BilanQuestionType::Number)
                        <input
                            id="answer-{{ $question->id }}"
                            name="answers[{{ $question->id }}]"
                            type="number"
                            step="any"
                            value="{{ $value }}"
                            class="{{ $inputClass }} mt-3"
                        >
                    @elseif ($question->type === BilanQuestionType::Choice)
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($question->options ?? [] as $option)
                                <label class="cursor-pointer">
                                    <input
                                        type="radio"
                                        name="answers[{{ $question->id }}]"
                                        value="{{ $option }}"
                                        class="peer sr-only"
                                        @checked((string) $value === (string) $option)
                                    >
                                    <span class="inline-flex min-h-11 items-center rounded-full border border-border px-3.5 text-sm text-muted transition-colors peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary">
                                        {{ $option }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @elseif ($question->type === BilanQuestionType::Rating)
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach (range(1, 5) as $score)
                                <label class="cursor-pointer">
                                    <input
                                        type="radio"
                                        name="answers[{{ $question->id }}]"
                                        value="{{ $score }}"
                                        class="peer sr-only"
                                        @checked((string) $value === (string) $score)
                                    >
                                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-border text-sm text-muted transition-colors peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary">
                                        {{ $score }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    @error($name)
                        <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </fieldset>
            @endforeach

            @error('answers')
                <p class="text-sm text-danger">{{ $message }}</p>
            @enderror

            <button
                type="submit"
                class="inline-flex min-h-11 w-full items-center justify-center rounded-lg bg-primary px-4 font-display text-xs font-bold uppercase tracking-wider text-bg transition-colors hover:bg-primary-hover"
            >
                Enregistrer
            </button>
        </form>
    </div>
</x-layouts.member>
