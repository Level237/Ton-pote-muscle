@php
    $inputClass = 'min-h-11 w-full rounded-lg border border-border bg-bg px-3 text-sm text-text outline-none placeholder:text-muted/50 focus:border-primary focus:ring-2 focus:ring-primary';
@endphp

<div class="space-y-6">
    <x-admin.form.field name="title" label="Titre">
        <input
            id="title"
            name="title"
            type="text"
            value="{{ old('title', $bilan->title) }}"
            required
            maxlength="120"
            class="{{ $inputClass }}"
        >
    </x-admin.form.field>

    <x-admin.form.field name="description" label="Description">
        <textarea
            id="description"
            name="description"
            rows="3"
            maxlength="2000"
            class="{{ $inputClass }} py-2.5"
        >{{ old('description', $bilan->description) }}</textarea>
    </x-admin.form.field>

    <div>
        <div class="mb-2 flex items-center justify-between gap-3">
            <h2 class="text-sm font-medium text-text">Questions</h2>
            <button type="button" id="add-bilan-question" class="text-sm font-medium text-primary hover:text-primary-hover">
                Ajouter
            </button>
        </div>

        <ul id="bilan-questions" class="space-y-3">
            @foreach ($questions as $index => $question)
                @include('admin.bilans._question', [
                    'index' => $index,
                    'question' => $question,
                ])
            @endforeach
        </ul>
        @error('questions')
            <p class="mt-1.5 text-sm text-danger">{{ $message }}</p>
        @enderror

        <template id="bilan-question-template">
            @include('admin.bilans._question', [
                'index' => '__INDEX__',
                'question' => [
                    'title' => '',
                    'type' => \App\Enums\BilanQuestionType::Text->value,
                    'options' => '',
                ],
            ])
        </template>
    </div>
</div>

<script>
    (() => {
        const list = document.getElementById('bilan-questions');
        const template = document.getElementById('bilan-question-template');
        const addButton = document.getElementById('add-bilan-question');
        if (!list || !template || !addButton) return;

        let nextIndex = list.querySelectorAll('.bilan-question').length;

        const syncOptions = (item) => {
            const selected = item.querySelector('.js-question-type:checked');
            const options = item.querySelector('.js-question-options');
            if (!options) return;
            options.classList.toggle('hidden', selected?.value !== 'choice');
        };

        const addQuestion = () => {
            const html = template.innerHTML.replaceAll('__INDEX__', String(nextIndex++));
            list.insertAdjacentHTML('beforeend', html);
            syncOptions(list.lastElementChild);
        };

        addButton.addEventListener('click', addQuestion);

        list.addEventListener('change', (event) => {
            if (!event.target.classList.contains('js-question-type')) return;
            const item = event.target.closest('.bilan-question');
            if (item) syncOptions(item);
        });

        list.addEventListener('click', (event) => {
            const item = event.target.closest('.bilan-question');
            if (!item) return;

            if (event.target.closest('.js-question-remove')) {
                if (list.querySelectorAll('.bilan-question').length > 1) {
                    item.remove();
                }
                return;
            }

            if (event.target.closest('.js-question-up') && item.previousElementSibling) {
                item.previousElementSibling.before(item);
                return;
            }

            if (event.target.closest('.js-question-down') && item.nextElementSibling) {
                item.nextElementSibling.after(item);
            }
        });

        list.querySelectorAll('.bilan-question').forEach(syncOptions);
    })();
</script>
