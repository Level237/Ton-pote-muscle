<?php

namespace App\Http\Requests\Admin;

use App\Support\ExerciseCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExerciseRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'instructions' => ['nullable', 'string', 'max:5000'],
            'body_parts' => ['nullable', 'array'],
            'body_parts.*' => ['string', Rule::in(array_keys(ExerciseCatalog::BODY_PARTS))],
            'equipments' => ['nullable', 'array'],
            'equipments.*' => ['string', Rule::in(array_keys(ExerciseCatalog::EQUIPMENTS))],
            'target_muscles' => ['nullable', 'string', 'max:500'],
            'secondary_muscles' => ['nullable', 'string', 'max:500'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'gif' => ['nullable', 'file', 'mimes:gif,webp', 'max:4096'],
            'remove_image' => ['sometimes', 'boolean'],
            'remove_gif' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'titre',
            'description' => 'description',
            'instructions' => 'consignes',
            'body_parts' => 'zones',
            'equipments' => 'matériel',
            'target_muscles' => 'muscles ciblés',
            'secondary_muscles' => 'muscles secondaires',
            'video_url' => 'vidéo',
            'image' => 'image',
            'gif' => 'gif',
        ];
    }
}
