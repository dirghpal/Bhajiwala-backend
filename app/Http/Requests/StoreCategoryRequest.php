<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|unique:categories,name',
            'description' => 'nullable',
            'image' => 'nullable',
            'status' => 'nullable|boolean'
        ];
    }
}