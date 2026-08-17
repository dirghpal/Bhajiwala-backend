<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        return handleApiRequest(function () {

            $wishlist = Wishlist::with('product')
                ->where('user_id', Auth::id())
                ->latest()
                ->get();

            $this->response['msg'] = 'Wishlist List';
            $this->response['data'] = $wishlist;

            return response()->json($this->response);
        });
    }

    public function store(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'product_id' => 'required|exists:products,id',
            ]);

            $wishlist = Wishlist::firstOrCreate([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
            ]);

            $this->response['msg'] = 'Product Added To Wishlist';
            $this->response['data'] = $wishlist->load('product');

            return response()->json($this->response, 201);
        });
    }

    public function destroy(Wishlist $wishlist)
    {
        return handleApiRequest(function () use ($wishlist) {

            if ($wishlist->user_id !== Auth::id()) {
                throw new \App\Http\Exceptions\ApiStatusException(
                    'Unauthorized',
                    403
                );
            }

            $wishlist->delete();

            $this->response['msg'] = 'Product Removed From Wishlist';
            $this->response['data'] = null;

            return response()->json($this->response);
        });
    }
}
