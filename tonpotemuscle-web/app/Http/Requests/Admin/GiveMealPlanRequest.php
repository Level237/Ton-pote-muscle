<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GiveMealPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'breakfast' => is_string($this->input('breakfast')) ? trim($this->input('breakfast')) : $this->input('breakfast'),
            'lunch' => is_string($this->input('lunch')) ? trim($this->input('lunch')) : $this->input('lunch'),
            'dinner' => is_string($this->input('dinner')) ? trim($this->input('dinner')) : $this->input('dinner'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'breakfast' => ['nullable', 'string', 'max:140'],
            'lunch' => ['nullable', 'string', 'max:140'],
            'dinner' => ['nullable', 'string', 'max:140'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (! filled($this->input('breakfast')) && ! filled($this->input('lunch')) && ! filled($this->input('dinner'))) {
                $validator->errors()->add('lunch', 'Donne au moins un repas.');
            }
        });
    }
}
