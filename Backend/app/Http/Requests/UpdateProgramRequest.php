<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'sometimes|string|max:255',
            'start_date'  => 'sometimes|date',
            'end_date'    => 'sometimes|date|after_or_equal:start_date',
            'no_of_days'  => 'sometimes|integer|min:1',
            'description' => 'nullable|string',
            'type'        => 'sometimes|in:travel,anniversary',
            'location'    => 'nullable|string|max:255',
            'country_id'  => 'nullable|exists:countries,id',
        ];
    }
}
