<?php

namespace App\Http\Requests\Admin;

use App\Enums\ProgramStatus;
use App\Support\WorkoutCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $slots = collect($this->input('slots', []))
            ->filter(fn ($slot) => filled($slot['day_number'] ?? null))
            ->map(function ($slot): array {
                $kind = ($slot['kind'] ?? 'workout') === 'rest' ? 'rest' : 'workout';

                return [
                    'day_number' => $slot['day_number'],
                    'kind' => $kind,
                    'workout_id' => $kind === 'rest' ? null : ($slot['workout_id'] ?? null),
                ];
            })
            ->filter(fn (array $slot): bool => $slot['kind'] === 'rest' || filled($slot['workout_id']))
            ->values()
            ->all();

        $this->merge(['slots' => $slots]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'objective' => ['nullable', 'string', 'max:120'],
            'level' => ['nullable', 'string', Rule::in(array_keys(WorkoutCatalog::DIFFICULTIES))],
            'starts_on' => ['required', 'date'],
            'ends_on' => ['required', 'date', 'after_or_equal:starts_on'],
            'status' => ['required', Rule::enum(ProgramStatus::class)],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['sometimes', 'boolean'],
            'slots' => ['required', 'array', 'min:1'],
            'slots.*.day_number' => ['required', 'integer', 'min:1', 'max:366'],
            'slots.*.kind' => ['required', Rule::in(['workout', 'rest'])],
            'slots.*.workout_id' => ['nullable', 'required_if:slots.*.kind,workout', 'uuid', 'exists:workouts,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $slots = collect($this->input('slots', []));
            $days = $slots->pluck('day_number')->all();

            if (count($days) !== count(array_unique($days))) {
                $validator->errors()->add('slots', 'Chaque jour du calendrier doit être unique.');
            }

            if ($slots->where('kind', 'workout')->isEmpty()) {
                $validator->errors()->add('slots', 'Ajoute au moins une séance. Les autres jours peuvent être au repos.');
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
            'objective' => 'objectif',
            'level' => 'niveau',
            'starts_on' => 'début',
            'ends_on' => 'fin',
            'status' => 'statut',
            'image' => 'visuel',
            'slots' => 'calendrier',
            'slots.*.day_number' => 'jour',
            'slots.*.kind' => 'type',
            'slots.*.workout_id' => 'séance',
        ];
    }
}
