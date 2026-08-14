<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Admin Product List',
            'data' => $products
        ]);
    }

    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'message' => 'Product Details',
            'data' => $product->load('category')
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'sometimes|required|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'unit' => 'sometimes|required|string',
            'featured' => 'nullable|boolean',
            'status' => 'nullable|boolean',
        ]);

        $data = $request->only([
            'name',
            'category_id',
            'description',
            'price',
            'discount_price',
            'stock',
            'unit',
            'featured',
            'status',
        ]);

        if ($request->has('name')) {
            $data['slug'] = Str::slug($request->name);
        }

        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Product Updated Successfully',
            'data' => $product->fresh()->load('category')
        ]);
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            deleteImage($product->image, 'products');
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product Deleted Successfully',
            'data' => null
        ]);
    }
}