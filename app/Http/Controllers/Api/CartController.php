<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::with('product')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Cart List',
            'data' => $cart
        ]);
    }

    public function store(Request $request)
    {
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1',
    ]);

    $cart = Cart::create([
        'user_id' => auth()->id(),
        'product_id' => $request->product_id,
        'quantity' => $request->quantity,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Product Added To Cart',
        'data' => $cart->load('product')
    ], 201);
    }
       
    public function update(Request $request, Cart $cart)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart->update([
            'quantity' => $request->quantity,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cart Updated Successfully',
            'data' => $cart->fresh()->load('product')
        ]);
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product Removed From Cart',
            'data' => null
        ]);
    }
}