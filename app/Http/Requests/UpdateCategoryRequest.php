<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|unique:categories,name,' . request()->category,
            'description' => 'nullable',
            'image' => 'nullable',
            'status' => 'required|boolean',
        ];
    }
}