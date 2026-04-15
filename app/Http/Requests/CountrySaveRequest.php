<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CountrySaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:24',
                Rule::unique('countries', 'name')->ignore($this->route('country')),
            ],
            'slug' => [
                'required',
                'string',
                'min:3',
                'max:24',
                Rule::unique('countries', 'slug')->ignore($this->route('country')),
            ],
            'description' => [
                'string',
                'nullable',
                'max:255'
            ],
            'flag_code' => [
                'string',
                'nullable',
                'max:255'
            ],
            'content' => [
                'string',
                'nullable'
            ],
        ];
    }
}
