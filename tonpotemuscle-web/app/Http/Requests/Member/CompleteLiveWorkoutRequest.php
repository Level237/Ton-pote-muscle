<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class CompleteLiveWorkoutRequest extends FormRequest
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
            'abandoned' => ['required', 'boolean'],
            'actual_duration_seconds' => ['nullable', 'integer', 'min:0', 'max:18000'],
            'exercises' => ['nullable', 'array'],
            'exercises.*.workout_exercise_id' => ['nullable', 'integer'],
            'exercises.*.exercise_id' => ['required', 'uuid', 'exists:exercises,id'],
            'exercises.*.position' => ['required', 'integer', 'min:1'],
            'exercises.*.started_at' => ['nullable', 'date'],
            'exercises.*.ended_at' => ['nullable', 'date'],
            'exercises.*.skipped' => ['required', 'boolean'],
        ];
    }
}
