<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize() { return auth()->check(); }
    public function rules()
    {
        return [
            'currency' => 'required|in:TRY,USD,EUR',
            'locale'   => 'required|in:tr,en',
        ];
    }
}
