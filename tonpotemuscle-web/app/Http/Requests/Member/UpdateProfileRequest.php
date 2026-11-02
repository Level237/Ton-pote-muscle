<?php

namespace App\Http\Requests\Member;

use App\Support\MemberProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
        $userId = $this->user()?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'alpha_dash',
                'min:3',
                'max:30',
                Rule::unique('users', 'username')->ignore($userId),
            ],
            'gender' => ['nullable', Rule::in(array_keys(MemberProfile::GENDERS))],
            'height_cm' => ['nullable', 'integer', 'min:100', 'max:250'],
            'weight_kg' => ['nullable', 'numeric', 'min:30', 'max:300'],
            'main_goal' => ['nullable', Rule::in(array_keys(MemberProfile::GOALS))],
            'fitness_level' => ['nullable', Rule::in(array_keys(MemberProfile::FITNESS_LEVELS))],
            'activity_level' => ['nullable', Rule::in(array_keys(MemberProfile::ACTIVITY_LEVELS))],
            'motivations' => ['nullable', 'array'],
            'motivations.*' => [Rule::in(array_keys(MemberProfile::MOTIVATIONS))],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function profileAttributes(): array
    {
        return [
            'name' => $this->string('name')->toString(),
            'username' => $this->string('username')->toString(),
            'gender' => $this->input('gender') ?: null,
            'height_cm' => $this->input('height_cm') ?: null,
            'weight_kg' => $this->input('weight_kg') ?: null,
            'main_goal' => $this->input('main_goal') ?: null,
            'fitness_level' => $this->input('fitness_level') ?: null,
            'activity_level' => $this->input('activity_level') ?: null,
            'motivations' => $this->input('motivations') ?: [],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nom',
            'username' => 'pseudo',
            'gender' => 'sexe',
            'height_cm' => 'taille',
            'weight_kg' => 'poids',
            'main_goal' => 'objectif',
            'fitness_level' => 'niveau d’entraînement',
            'activity_level' => 'niveau d’activité',
            'motivations' => 'motivations',
        ];
    }
}
