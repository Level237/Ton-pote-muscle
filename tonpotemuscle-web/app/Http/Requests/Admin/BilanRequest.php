<?php

namespace App\Http\Requests\Admin;

use App\Enums\BilanQuestionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class BilanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $questions = collect($this->input('questions', []))
            ->map(function ($question): array {
                $lines = preg_split('/\r\n|\r|\n/', (string) ($question['options'] ?? '')) ?: [];
                $options = collect($lines)
                    ->map(fn ($line) => trim((string) $line))
                    ->filter()
                    ->values()
                    ->all();

                $type = $question['type'] ?? BilanQuestionType::Text->value;

                return [
                    'title' => trim((string) ($question['title'] ?? '')),
                    'type' => $type,
                    'options' => $type === BilanQuestionType::Choice->value ? $options : [],
                ];
            })
            ->filter(fn (array $question): bool => $question['title'] !== '')
            ->values()
            ->all();

        $this->merge(['questions' => $questions]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.title' => ['required', 'string', 'max:180'],
            'questions.*.type' => ['required', Rule::enum(BilanQuestionType::class)],
            'questions.*.options' => ['nullable', 'array'],
            'questions.*.options.*' => ['string', 'max:120'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach ($this->input('questions', []) as $index => $question) {
                if (($question['type'] ?? null) !== BilanQuestionType::Choice->value) {
                    continue;
                }

                if (count($question['options'] ?? []) < 2) {
                    $validator->errors()->add(
                        "questions.{$index}.options",
                        'Indique au moins deux choix, un par ligne.',
                    );
                }
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'titre',
            'description' => 'description',
            'questions' => 'questions',
            'questions.*.title' => 'question',
            'questions.*.type' => 'type',
            'questions.*.options' => 'choix',
        ];
    }
}
