<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize() { return auth()->check(); }

    public function rules()
    {
        $id = $this->route('id');
        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $id . ',id,user_id,' . auth()->id(),
        ];
    }
}
