<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class StoreMealNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isMember() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'note' => is_string($this->input('note')) ? trim($this->input('note')) : $this->input('note'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'note' => ['nullable', 'string', 'max:140'],
        ];
    }
}
