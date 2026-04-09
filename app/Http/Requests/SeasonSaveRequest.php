<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SeasonSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $seasonId = $this->route('season')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('seasons', 'name')
                    ->where(fn($query) => $query->where('competition_id', $this->competition_id))
                    ->ignore($seasonId),
            ],
            'competition_id' => [
                'required',
                'integer',
                'exists:competitions,id',
            ],
        ];
    }
}
