<?php

namespace App\Http\Requests\Member;

use App\Support\MemberMeals;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreMealCheckinRequest extends FormRequest
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
            'meal' => ['required', 'string', Rule::in(array_keys(MemberMeals::SLOTS))],
            'status' => ['required', 'string', Rule::in(array_keys(MemberMeals::STATUSES))],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $meal = $this->input('meal');

            if (is_string($meal) && isset(MemberMeals::SLOTS[$meal]) && ! MemberMeals::isPrescribed($this->user(), $meal)) {
                $validator->errors()->add('meal', 'Ce repas n’a pas été donné par ton coach.');
            }
        });
    }
}
