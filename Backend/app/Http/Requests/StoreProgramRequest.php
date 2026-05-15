<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'no_of_days'  => 'required|integer|min:1',
            'description' => 'nullable|string',
            'type'        => 'required|in:travel,anniversary',
            'location'    => 'nullable|string|max:255',
            'country_id'  => 'nullable|exists:countries,id',
        ];
    }
}
