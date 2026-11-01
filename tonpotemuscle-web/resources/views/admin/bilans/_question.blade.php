@php
    use App\Enums\BilanQuestionType;

    $inputClass = 'min-h-11 w-full rounded-lg border border-border bg-bg px-3 text-sm text-text outline-none focus:border-primary focus:ring-2 focus:ring-primary';
    $type = $question['type'] ?? BilanQuestionType::Text->value;
    $optionsText = is_array($question['options'] ?? null)
        ? implode("\n", $question['options'])
        : ($question['options'] ?? '');
@endphp

<li class="bilan-question space-y-4 rounded-xl border border-border bg-bg p-4">
    <div class="flex items-start gap-3">
        <div class="min-w-0 flex-1">
            <x-admin.form.field :name="'questions.'.$index.'.title'" label="Question">
                <input
                    id="questions-{{ $index }}-title"
                    name="questions[{{ $index }}][title]"
                    type="text"
                    maxlength="180"
                    value="{{ $question['title'] ?? '' }}"
                    class="{{ $inputClass }}"
                >
            </x-admin.form.field>
        </div>

        <div class="flex shrink-0 gap-1 pt-7">
            <button type="button" class="js-question-up min-h-11 px-2 text-sm text-muted hover:text-text" aria-label="Monter">
                <i class="fa-solid fa-arrow-up" aria-hidden="true"></i>
            </button>
            <button type="button" class="js-question-down min-h-11 px-2 text-sm text-muted hover:text-text" aria-label="Descendre">
                <i class="fa-solid fa-arrow-down" aria-hidden="true"></i>
            </button>
            <button type="button" class="js-question-remove min-h-11 px-2 text-sm text-muted hover:text-danger" aria-label="Retirer">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <fieldset>
        <legend class="mb-2 text-sm font-medium text-text">Type</legend>
        <div class="flex flex-wrap gap-2">
            @foreach (BilanQuestionType::cases() as $questionType)
                <label class="cursor-pointer">
                    <input
                        type="radio"
                        name="questions[{{ $index }}][type]"
                        value="{{ $questionType->value }}"
                        class="js-question-type peer sr-only"
                        @checked($type === $questionType->value)
                    >
                    <span class="inline-flex min-h-11 items-center rounded-full border border-border px-3.5 text-sm text-muted transition-colors peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary">
                        {{ $questionType->label() }}
                    </span>
                </label>
            @endforeach
        </div>
        @error('questions.'.$index.'.type')
            <p class="mt-1.5 text-sm text-danger">{{ $message }}</p>
        @enderror
    </fieldset>

    <div class="js-question-options {{ $type === BilanQuestionType::Choice->value ? '' : 'hidden' }}">
        <x-admin.form.field :name="'questions.'.$index.'.options'" label="Choix" hint="Un choix par ligne.">
            <textarea
                id="questions-{{ $index }}-options"
                name="questions[{{ $index }}][options]"
                rows="5"
                class="{{ $inputClass }} py-2.5"
            >{{ $optionsText }}</textarea>
        </x-admin.form.field>
    </div>
</li>
