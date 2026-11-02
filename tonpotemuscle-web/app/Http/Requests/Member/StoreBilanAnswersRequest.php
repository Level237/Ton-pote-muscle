<?php

namespace App\Http\Requests\Member;

use App\Enums\BilanQuestionType;
use App\Models\Bilan;
use App\Models\BilanQuestion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBilanAnswersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isMember() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'answers' => ['required', 'array'],
            'answers.*' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $bilan = $this->route('bilan');

            if (! $bilan instanceof Bilan) {
                return;
            }

            $bilan->loadMissing('questions');
            $answers = $this->input('answers', []);

            foreach ($bilan->questions as $question) {
                $value = trim((string) ($answers[$question->id] ?? ''));
                $key = "answers.{$question->id}";

                if ($value === '') {
                    $validator->errors()->add($key, 'Réponds à cette question.');

                    continue;
                }

                $this->validateAnswer($validator, $question, $value, $key);
            }
        });
    }

    private function validateAnswer(Validator $validator, BilanQuestion $question, string $value, string $key): void
    {
        match ($question->type) {
            BilanQuestionType::Number => $this->validateNumber($validator, $value, $key),
            BilanQuestionType::Rating => $this->validateRating($validator, $value, $key),
            BilanQuestionType::Choice => $this->validateChoice($validator, $question, $value, $key),
            BilanQuestionType::Text => null,
        };
    }

    private function validateNumber(Validator $validator, string $value, string $key): void
    {
        if (! is_numeric($value)) {
            $validator->errors()->add($key, 'Indique un nombre.');
        }
    }

    private function validateRating(Validator $validator, string $value, string $key): void
    {
        if (! in_array($value, ['1', '2', '3', '4', '5'], true)) {
            $validator->errors()->add($key, 'Choisis une note de 1 à 5.');
        }
    }

    private function validateChoice(Validator $validator, BilanQuestion $question, string $value, string $key): void
    {
        $options = $question->options ?? [];

        if (! in_array($value, $options, true)) {
            $validator->errors()->add($key, 'Choisis une option.');
        }
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'answers' => 'réponses',
        ];
    }
}
