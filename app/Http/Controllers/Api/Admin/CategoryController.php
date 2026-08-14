<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        // Search
        if ($request->filled('search')) {
            $query->where(
                'name',
                'like',
                '%' . $request->search . '%'
            );
        }

        // Status Filter
        if ($request->has('status')) {
            $query->where(
                'status',
                filter_var(
                    $request->status,
                    FILTER_VALIDATE_BOOLEAN
                )
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

        return response()->json([
            'success' => true,
            'message' => 'Admin Category List',
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

    public function show(Category $category)
    {
        return response()->json([
            'success' => true,
            'message' => 'Category Details',
            'data' => $category
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'sometimes|required|string|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $data = $request->only([
            'name',
            'description',
            'status',
        ]);

        if ($request->has('name')) {
            $data['slug'] = Str::slug($request->name);
        }

        $category->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Category Updated Successfully',
            'data' => $category->fresh()
        ]);
    }

    public function destroy(Category $category)
    {
        if ($category->image) {
            deleteImage(
                $category->image,
                'categories'
            );
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category Deleted Successfully',
            'data' => null
        ]);
    }
}  