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
        return handleApiRequest(function () {

            $products = Product::with('category')
                ->latest()
                ->get();

            $this->response['msg'] = 'Admin Product List';
            $this->response['data'] = $products;

            return response()->json($this->response);
        });
    }

    public function show(Product $product)
    {
        return handleApiRequest(function () use ($product) {

            $this->response['msg'] = 'Product Details';
            $this->response['data'] = $product->load('category');

            return response()->json($this->response);
        });
    }

    public function update(Request $request, Product $product)
    {
        return handleApiRequest(function () use ($request, $product) {

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

            $this->response['msg'] = 'Product Updated Successfully';
            $this->response['data'] = $product->fresh()->load('category');

            return response()->json($this->response);
        });
    }

    public function destroy(Product $product)
    {
        return handleApiRequest(function () use ($product) {

            if ($product->image) {
                deleteImage(
                    $product->image,
                    'products'
                );
            }

            $product->delete();

            $this->response['msg'] = 'Product Deleted Successfully';
            $this->response['data'] = null;

            return response()->json($this->response);
        });
    }
}