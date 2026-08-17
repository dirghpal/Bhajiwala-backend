<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $query = Product::with('category')
                ->where('status', true);

            // Search
            if ($request->filled('search')) {
                $query->where(
                    'name',
                    'like',
                    '%' . $request->search . '%'
                );
            }

            // Category Filter
            if ($request->filled('category_id')) {
                $query->where(
                    'category_id',
                    $request->category_id
                );
            }

            // Minimum Price
            if ($request->filled('min_price')) {
                $query->where(
                    'price',
                    '>=',
                    $request->min_price
                );
            }

            // Maximum Price
            if ($request->filled('max_price')) {
                $query->where(
                    'price',
                    '<=',
                    $request->max_price
                );
            }

            // Featured Filter
            if ($request->has('featured')) {
                $query->where(
                    'featured',
                    filter_var(
                        $request->featured,
                        FILTER_VALIDATE_BOOLEAN
                    )
                );
            }

            // Sorting
            $sort = $request->sort ?? 'newest';

            switch ($sort) {

                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;

                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;

                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;

                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;

                default:
                    $query->latest();
                    break;
            }

            // Pagination
            $products = $query->paginate(10);

            $this->response['msg'] = 'Product List';

            $this->response['data'] = [
                'products' => $products->items(),

                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ],
            ];

            return response()->json($this->response);
        });
    }

    public function store(StoreProductRequest $request)
    {
        return handleApiRequest(function () use ($request) {

            $image = null;

            if ($request->image) {

                moveImage(
                    $request->image,
                    'temp',
                    'products'
                );

                $image = $request->image;
            }

            $product = Product::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'stock' => $request->stock,
                'unit' => $request->unit,
                'image' => $image,
                'featured' => $request->featured ?? false,
                'status' => $request->status ?? true,
            ]);

            $this->response['msg'] = 'Product Created Successfully';
            $this->response['data'] = $product->load('category');

            return response()->json($this->response, 201);
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

    public function update(
        UpdateProductRequest $request,
        Product $product
    ) {
        return handleApiRequest(function () use ($request, $product) {

            $image = $product->image;

            if (
                $request->image &&
                $request->image != $product->image
            ) {

                deleteImage(
                    $product->image,
                    'products'
                );

                moveImage(
                    $request->image,
                    'temp',
                    'products'
                );

                $image = $request->image;
            }

            $product->update([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'stock' => $request->stock,
                'unit' => $request->unit,
                'image' => $image,
                'featured' => $request->featured ?? false,
                'status' => $request->status ?? true,
            ]);

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