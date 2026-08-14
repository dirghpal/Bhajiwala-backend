<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [ 
            'category_id'     => 'required|exists:categories,id',
            'name'            => 'required|unique:products,name',
            'description'     => 'nullable',
            'price'           => 'required|numeric|min:0',
            'discount_price'  => 'nullable|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'unit'            => 'required|string|max:20',
            'image'           => 'nullable|string',
            'featured'        => 'nullable|boolean',
            'status'          => 'nullable|boolean',
        ];
    }
}