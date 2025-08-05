<?php

namespace App\Http\Requests\Goal;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGoalRequest extends FormRequest
{
    public function authorize() { return auth()->check(); }

    public function rules()
    {
        return [
            'title'         => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount'=> 'nullable|numeric|min:0',
            'end_date'      => 'required|date|after:today',
        ];
    }
}
