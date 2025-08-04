<?php

namespace App\Http\Requests\Budget;

use Illuminate\Foundation\Http\FormRequest;

class StoreBudgetRequest extends FormRequest
{
    public function authorize() { return auth()->check(); }

    public function rules()
    {
        return [
            'category_id' => 'required|exists:categories,id',
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
