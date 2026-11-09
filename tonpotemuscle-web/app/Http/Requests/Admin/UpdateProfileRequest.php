<?php

namespace App\Http\Requests\Admin;

use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => PhoneNumber::normalize($this->input('phone')),
        ]);
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
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($userId),
                PhoneNumber::rule(),
            ],
            'current_password' => ['required_with:password', 'nullable', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function profileAttributes(): array
    {
        $attributes = [
            'name' => $this->string('name')->toString(),
            'username' => $this->string('username')->toString(),
            'email' => $this->string('email')->toString(),
            'phone' => $this->input('phone') ?: null,
        ];

        if ($this->filled('password')) {
            $attributes['password'] = $this->string('password')->toString();
        }

        return $attributes;
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nom',
            'username' => 'pseudo',
            'email' => 'adresse e-mail',
            'phone' => 'numéro de téléphone',
            'current_password' => 'mot de passe actuel',
            'password' => 'nouveau mot de passe',
        ];
    }
}
