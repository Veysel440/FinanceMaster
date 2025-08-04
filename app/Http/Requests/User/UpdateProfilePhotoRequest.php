<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilePhotoRequest extends FormRequest
{
    public function authorize() { return auth()->check(); }
    public function rules()
    {
        return [
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
