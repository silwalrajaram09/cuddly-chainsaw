<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommitteeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => 'required|string|max:255',
            'parent_id'  => 'nullable|exists:committees,id',
            'updated_by' => 'nullable|exists:users,user_id',
        ];
    }
}
