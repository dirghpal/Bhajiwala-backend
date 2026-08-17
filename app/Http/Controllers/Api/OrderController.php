<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        return handleApiRequest(function () {

            $orders = Order::with('items.product')
                ->where('user_id',  Auth::id())
                ->latest()
                ->get();

            $this->response['msg'] = 'Order List';
            $this->response['data'] = $orders;

            return response()->json($this->response);
        });
    }

    public function store(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'address' => 'required|string',
                'payment_method' => 'required|in:cod',
            ]);

            $cart = Cart::with('product')
                ->where('user_id', Auth::id())
                ->get();

            if ($cart->isEmpty()) {
                throw new \App\Http\Exceptions\ApiStatusException(
                    'Cart is empty',
                    422
                );
            }

            DB::beginTransaction();

            try {

                // Check Stock

                foreach ($cart as $item) {

                    if (!$item->product) {
                        throw new \App\Http\Exceptions\ApiStatusException(
                            'Product not found',
                            404
                        );
                    }

                    if ($item->product->stock < $item->quantity) {
                        throw new \App\Http\Exceptions\ApiStatusException(
                            "Insufficient stock for {$item->product->name}",
                            422
                        );
                    }
                }

                // Calculate Total

                $totalAmount = 0;

                foreach ($cart as $item) {

                    $totalAmount +=
                        $item->product->price * $item->quantity;
                }

                // Create Order

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                    'payment_method' => $request->payment_method,
                    'payment_status' => 'pending',
                    'address' => $request->address,
                ]);

                // Create Order Items + Reduce Stock

                foreach ($cart as $item) {

                    $itemTotal =
                        $item->product->price * $item->quantity;

                    $order->items()->create([
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->price,
                        'total' => $itemTotal,
                    ]);

                    // Reduce Product Stock

                    $item->product->decrement(
                        'stock',
                        $item->quantity
                    );
                }

                // Clear Cart

                Cart::where('user_id', Auth::id())->delete();

                // Commit Transaction

                DB::commit();

                $this->response['msg'] = 'Order Created Successfully';
                $this->response['data'] =
                    $order->load('items.product');

                return response()->json($this->response, 201);

            } catch (\Throwable $e) {

                DB::rollBack();

                throw $e;
            }
        });
    }

    public function show(Order $order)
    {
        return handleApiRequest(function () use ($order) {

            if ($order->user_id !== Auth::id()) {
                throw new \App\Http\Exceptions\ApiStatusException(
                    'Unauthorized',
                    403
                );
            }

            $this->response['msg'] = 'Order Details';
            $this->response['data'] =
                $order->load('items.product');

            return response()->json($this->response);
        });
    }

    public function update(Request $request, Order $order)
    {
        return handleApiRequest(function () use ($request, $order) {

            if ($order->user_id !== Auth::id()) {
                throw new \App\Http\Exceptions\ApiStatusException(
                    'Unauthorized',
                    403
                );
            }

            $request->validate([
                'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled',
            ]);

            $order->update([
                'status' => $request->status,
            ]);

            $this->response['msg'] =
                'Order Updated Successfully';

            $this->response['data'] =
                $order->fresh()->load('items.product');

            return response()->json($this->response);
        });
    }
}