<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class ReportFilterRequest extends FormRequest
{
    public function authorize() { return auth()->check(); }

    public function rules()
    {
        return [
            'period' => 'nullable|in:daily,weekly,monthly,yearly,custom',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ];
    }
}
