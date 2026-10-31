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
            ->filter(fn ($slot) => filled($slot['workout_id'] ?? null) && filled($slot['day_number'] ?? null))
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
            'slots.*.workout_id' => ['required', 'uuid', 'exists:workouts,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $days = collect($this->input('slots', []))->pluck('day_number')->all();

            if (count($days) !== count(array_unique($days))) {
                $validator->errors()->add('slots', 'Chaque jour du calendrier doit être unique.');
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
            'slots.*.workout_id' => 'séance',
        ];
    }
}
