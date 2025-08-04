<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }
    public function rules()
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore(auth()->id())
            ],
            'password' => 'nullable|string|min:8|confirmed',
        ];
    }
}
