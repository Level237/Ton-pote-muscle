<?php

namespace App\Http\Requests\Admin;

use App\Support\MemberHydration;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHydrationGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'hydration_goal_glasses' => [
                'required',
                'integer',
                'min:'.MemberHydration::MIN_GOAL,
                'max:'.MemberHydration::MAX_GOAL,
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'hydration_goal_glasses' => 'objectif',
        ];
    }
}
