<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        return handleApiRequest(function () {

            $userId = Auth::id();

            $cart = Cart::with('product')
                ->where('user_id', $userId)
                ->latest()
                ->get();

            $this->response['msg'] = 'Cart List';
            $this->response['data'] = $cart;

            return response()->json($this->response);
        });
    }

    public function store(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
            ]);

            $userId = Auth::id();

            $cart = Cart::create([
                'user_id' => $userId,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);

            $this->response['msg'] = 'Product Added To Cart';
            $this->response['data'] = $cart->load('product');

            return response()->json($this->response, 201);
        });
    }

    public function update(Request $request, Cart $cart)
    {
        return handleApiRequest(function () use ($request, $cart) {

            if ($cart->user_id !== Auth::id()) {
                throw new \App\Http\Exceptions\ApiStatusException(
                    'Unauthorized',
                    403
                );
            }

            $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);

            $cart->update([
                'quantity' => $request->quantity,
            ]);

            $this->response['msg'] = 'Cart Updated Successfully';
            $this->response['data'] = $cart->fresh()->load('product');

            return response()->json($this->response);
        });
    }

    public function destroy(Cart $cart)
    {
        return handleApiRequest(function () use ($cart) {

            if ($cart->user_id !== Auth::id()) {
                throw new \App\Http\Exceptions\ApiStatusException(
                    'Unauthorized',
                    403
                );
            }

            $cart->delete();

            $this->response['msg'] = 'Product Removed From Cart';
            $this->response['data'] = null;

            return response()->json($this->response);
        });
    }
}