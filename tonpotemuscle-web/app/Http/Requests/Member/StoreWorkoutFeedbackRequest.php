<?php

namespace App\Http\Requests\Member;

use App\Support\WorkoutFeedback;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkoutFeedbackRequest extends FormRequest
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
            'difficulty_feedback' => ['required', 'integer', Rule::in(WorkoutFeedback::values())],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'difficulty_feedback' => 'ressenti',
        ];
    }
}
