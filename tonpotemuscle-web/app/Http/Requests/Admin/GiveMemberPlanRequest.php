<?php

namespace App\Http\Requests\Admin;

use App\Enums\ProgramStatus;
use App\Models\Program;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class GiveMemberPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $planned = $this->input('planned_on');

        if (is_string($planned) && $planned !== '') {
            $this->merge(['planned_on' => [$planned]]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'mode' => ['required', Rule::in(['workout', 'program'])],
            'workout_id' => ['nullable', 'required_if:mode,workout', 'uuid', 'exists:workouts,id'],
            'planned_on' => ['nullable', 'required_if:mode,workout', 'array', 'min:1'],
            'planned_on.*' => ['date'],
            'program_id' => ['nullable', 'required_if:mode,program', 'string'],
            'program_week' => ['nullable', 'integer', 'min:1', 'max:52'],
            'target_week' => ['nullable', 'required_if:mode,program', Rule::in(['this', 'next'])],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->input('mode') !== 'program') {
                return;
            }

            $programId = $this->input('program_id');

            if ($programId === 'current') {
                return;
            }

            $exists = Program::query()
                ->whereKey($programId)
                ->where('status', '!=', ProgramStatus::Draft)
                ->exists();

            if (! $exists) {
                $validator->errors()->add('program_id', 'Choisis un programme.');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'mode' => 'type',
            'workout_id' => 'séance',
            'planned_on' => 'date',
            'program_id' => 'programme',
            'program_week' => 'semaine du programme',
            'target_week' => 'période',
        ];
    }
}
