<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlist = Wishlist::with('product')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist List',
            'data' => $wishlist
        ]);
    }

    public function store(Request $request)
    
    {
    $request->validate([
        'product_id' => 'required|exists:products,id',
    ]);

    $wishlist = Wishlist::firstOrCreate([
        'user_id' => auth()->id(),
        'product_id' => $request->product_id,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Product Added To Wishlist',
        'data' => $wishlist->load('product')
    ], 201);
    }

    public function destroy(Wishlist $wishlist)
    {
        $wishlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product Removed From Wishlist',
            'data' => null
        ]);
    }
}