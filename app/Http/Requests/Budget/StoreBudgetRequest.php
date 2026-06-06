<?php

namespace App\Http\Requests\Budget;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBudgetRequest extends FormRequest
{
    public function authorize() { return auth()->check(); }

    public function rules()
    {
        return [
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(function ($query) {
                    $query->where('user_id', auth()->id())
                          ->orWhere('is_default', true);
                }),
            ],
            'amount'      => 'required|numeric|min:0.01',
            'month'       => 'required|date_format:Y-m',
        ];
    }

    public function validatedWithMonth()
    {
        $validated = $this->validated();
        $validated['month'] = $validated['month'] . '-01';
        return $validated;
    }
}
