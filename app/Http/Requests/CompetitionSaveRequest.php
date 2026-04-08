<?php

namespace App\Http\Requests;

use App\Enums\CompetitionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompetitionSaveRequest extends FormRequest
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
                Rule::unique('competitions', 'name')->ignore($this->route('competition')),
            ],
            'slug' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('competitions', 'slug')->ignore($this->route('competition')),
            ],
            'description' => [
                'string',
                'nullable',
                'max:255'
            ],
            'content' => [
                'string',
                'nullable'
            ],
            'country_id' => [
                'required',
                'integer',
                'exists:countries,id',
            ],
            'type' => [
                'required',
                Rule::in(array_column(CompetitionType::cases(), 'value')),
            ],
        ];
    }
}
