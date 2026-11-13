<?php

namespace App\Http\Requests\Admin;

use App\Enums\ProgramStatus;
use App\Models\Program;
use App\Models\Workout;
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
            'loads' => ['nullable', 'array'],
            'loads.*' => ['nullable', 'numeric', 'min:0.5', 'max:200'],
            'week_loads' => ['nullable', 'array'],
            'week_loads.*' => ['nullable', 'numeric', 'min:0.5', 'max:200'],
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
     * @return array<string, float>
     */
    public function prescribedLoads(Workout $workout): array
    {
        $raw = $this->input('loads', []);

        if (! is_array($raw)) {
            return [];
        }

        $allowed = $workout->items
            ->filter(fn ($row) => $row->exercise?->usesLoad())
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        $loads = [];

        foreach ($raw as $id => $kg) {
            if ($kg === null || $kg === '') {
                continue;
            }

            if (! in_array((string) $id, $allowed, true)) {
                continue;
            }

            $loads[(string) $id] = round((float) $kg, 1);
        }

        return $loads;
    }

    /**
     * @return array<string, float>
     */
    public function weekLoads(): array
    {
        $raw = $this->input('week_loads', []);

        if (! is_array($raw)) {
            return [];
        }

        $loads = [];

        foreach ($raw as $exerciseId => $kg) {
            if ($kg === null || $kg === '') {
                continue;
            }

            $loads[(string) $exerciseId] = round((float) $kg, 1);
        }

        return $loads;
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
            'loads' => 'charges',
            'week_loads' => 'charges',
        ];
    }
}
