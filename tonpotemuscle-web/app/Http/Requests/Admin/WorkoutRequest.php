<?php

namespace App\Http\Requests\Admin;

use App\Support\WorkoutCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class WorkoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $items = collect($this->input('items', []))
            ->filter(fn ($item) => filled($item['exercise_id'] ?? null))
            ->map(function ($item): array {
                $item['sets'] = filled($item['sets'] ?? null) ? $item['sets'] : 1;

                return $item;
            })
            ->values()
            ->all();

        $this->merge(['items' => $items]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'category_id' => ['nullable', 'uuid', 'exists:workout_categories,id'],
            'difficulty' => ['nullable', 'string', Rule::in(array_keys(WorkoutCatalog::DIFFICULTIES))],
            'notes' => ['nullable', 'string', 'max:2000'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_thumbnail' => ['sometimes', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.exercise_id' => ['required', 'uuid', 'exists:exercises,id'],
            'items.*.sets' => ['required', 'integer', 'min:1', 'max:20'],
            'items.*.reps' => ['nullable', 'integer', 'min:1', 'max:999'],
            'items.*.duration_seconds' => ['nullable', 'integer', 'min:1', 'max:3600'],
            'items.*.rest_seconds' => ['required', 'integer', 'min:0', 'max:600'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach ($this->input('items', []) as $index => $item) {
                if (blank($item['reps'] ?? null) && blank($item['duration_seconds'] ?? null)) {
                    $validator->errors()->add(
                        "items.{$index}.reps",
                        'Indique des répétitions ou une durée.',
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
            'category_id' => 'catégorie',
            'difficulty' => 'niveau',
            'notes' => 'note',
            'thumbnail' => 'visuel',
            'items' => 'exercices',
            'items.*.exercise_id' => 'exercice',
            'items.*.sets' => 'séries',
            'items.*.reps' => 'répétitions',
            'items.*.duration_seconds' => 'durée',
            'items.*.rest_seconds' => 'repos',
        ];
    }
}
