<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClubSaveRequest extends FormRequest
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
                'max:255',
                Rule::unique('clubs', 'name')->ignore($this->route('club')),
            ],
            'slug' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'alpha_dash',
                Rule::unique('clubs', 'slug')->ignore($this->route('club')),
            ],
            'country_id' => [
                'required',
                'integer',
                'exists:countries,id',
            ],
            'nickname' => [
                'nullable',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
                'max:255',
            ],
            'content' => [
                'nullable',
                'string',
            ],
            'founded_at' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],
            'destroyed_at' => [
                'nullable',
                'date',
                'after_or_equal:founded_at',
            ],
            'stadium' => [
                'nullable',
                'string',
                'max:255',
            ],
            'city' => [
                'nullable',
                'string',
                'max:255',
            ],
            'address' => [
                'nullable',
                'string',
                'max:255',
            ],
            'names' => [
                'nullable',
                'array'
            ],
            'names.*.name' => [
                'required',
                'string',
                'max:255'
            ],
            'names.*.from_year' => [
                'nullable',
                'integer'
            ],
            'names.*.to_year' => [
                'nullable',
                'integer'
            ],
            'names.*.note' => [
                'nullable',
                'string',
                'max:255'
            ],
        ];
    }
}
