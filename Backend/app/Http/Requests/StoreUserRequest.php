<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name'           => 'required|string|max:255',
            'designation'         => 'nullable|string|max:255',
            'country_id'          => 'nullable|integer',
            'type'                => 'required|in:admin,user',
            'member_type'         => 'required|in:yearly,lifetime',
            'fb_link'             => 'nullable|url',
            'youtube'             => 'nullable|url',
            'tiktok'              => 'nullable|url',
            'profile_description' => 'nullable|string',
            'address'             => 'nullable|string',
            'started_date'        => 'nullable|date',
            'end_date'            => 'nullable|date|after_or_equal:started_date',
            'email'               => 'nullable|email|unique:users,email',
            'password'            => 'nullable|string|min:6',
            'photo'               => 'nullable|image|max:2048',
        ];
    }
}
