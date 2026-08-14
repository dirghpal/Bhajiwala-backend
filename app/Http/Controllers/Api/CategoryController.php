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
    $query = Category::where('status', true);

    // Search
    if ($request->filled('search')) {
        $query->where(
            'name',
            'like',
            '%' . $request->search . '%'
        );
    }


    // Status Filter
    // if ($request->has('status')) {
    //     $query->where(
    //         'status',
    //         filter_var(
    //             $request->status,
    //             FILTER_VALIDATE_BOOLEAN
    //         )
    //     );
    // }

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

    return response()->json([
        'success' => true,
        'message' => 'Category List',
        'data' => [
            'categories' => $categories->items(),

            'pagination' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
            ],
        ],
    ]);
    }

    public function store(StoreCategoryRequest $request)
    {
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

        return response()->json([
            'success' => true,
            'message' => 'Category Created Successfully',
            'data' => $category
        ], 201);
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
        $image = $category->image;

        if ($request->image && $request->image != $category->image) {
            deleteImage($category->image, 'categories');

            moveImage($request->image, 'temp', 'categories');

            $image = $request->image;
        }

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $image,
            'description' => $request->description,
            'status' => $request->status ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category Updated Successfully',
            'data' => $category->fresh()
        ]);
    }

    public function destroy(Category $category)
    {
        deleteImage($category->image, 'categories');

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category Deleted Successfully',
            'data' => null
        ]);
    }
}