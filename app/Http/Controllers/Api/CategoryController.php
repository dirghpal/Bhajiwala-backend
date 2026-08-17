<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    
    {
    return handleApiRequest(function () use ($request) {

        $query = Category::where('status', true);

        // Search
        if ($request->filled('search')) {
            $query->where(
                'name',
                'like',
                '%' . $request->search . '%'
            );
        }

        // Sorting
        $sort = $request->sort ?? 'newest';

        switch ($sort) {

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
        $categories = $query->paginate(10);

        $this->response['msg'] = 'Category List';

        $this->response['data'] = [
            'categories' => $categories->items(),
            'pagination' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
            ],
        ];

        return response()->json($this->response);
    });
    }

    public function store(StoreCategoryRequest $request)
    
    {
    return handleApiRequest(function () use ($request) {

        $image = null;

        if ($request->image) {
            moveImage($request->image, 'temp', 'categories');
            $image = $request->image;
        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $image,
            'description' => $request->description,
            'status' => $request->status ?? true,
        ]);

        $this->response['msg'] = 'Category Created Successfully';
        $this->response['data'] = $category;

        return response()->json($this->response, 201);
    });
    }


    public function show(Category $category)
    {
    return handleApiRequest(function () use ($category) {

        $this->response['msg'] = 'Category Details';
        $this->response['data'] = $category;

        return response()->json($this->response);
    });
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
    return handleApiRequest(function () use ($request, $category) {

        $image = $category->image;

        if ($request->image && $request->image != $category->image) {

            deleteImage($category->image, 'categories');

            moveImage(
                $request->image,
                'temp',
                'categories'
            );

            $image = $request->image;
        }

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $image,
            'description' => $request->description,
            'status' => $request->status ?? true,
        ]);

        $this->response['msg'] = 'Category Updated Successfully';
        $this->response['data'] = $category->fresh();

        return response()->json($this->response);
    });
    }

    public function destroy(Category $category)
    
    {
    return handleApiRequest(function () use ($category) {

        if ($category->image) {
            deleteImage(
                $category->image,
                'categories'
            );
        }

        $category->delete();

        $this->response['msg'] = 'Category Deleted Successfully';
        $this->response['data'] = null;

        return response()->json($this->response);
    });
    }
}