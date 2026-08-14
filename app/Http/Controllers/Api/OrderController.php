<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.product')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Order List',
            'data' => $orders
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'payment_method' => 'required|in:cod',
        ]);

        $cart = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get();

        if ($cart->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty',
                'data' => null
            ], 422);
        }

        DB::beginTransaction();

        try {

            //chack stock

            foreach ($cart as $item) {

                if (!$item->product) {
                    throw new \Exception('Product not found');
                }

                if ($item->product->stock < $item->quantity) {
                    throw new \Exception(
                        "Insufficient stock for {$item->product->name}"
                    );
                }
            }

            // Calculate Total
           
            $totalAmount = 0;

            foreach ($cart as $item) {

                $totalAmount +=
                    $item->product->price * $item->quantity;
            }

            
            //Create Order
           

            $order = Order::create([
                'user_id' => auth()->id(),
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

                // Reduce product stock
                $item->product->decrement(
                    'stock',
                    $item->quantity
                );
            }

            // clear cart 

            Cart::where('user_id', auth()->id())->delete();


            // Commit Transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order Created Successfully',
                'data' => $order->load('items.product')
            ], 201);

        } catch (\Throwable $e) {

        //Roll back
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'data' => null
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order Details',
            'data' => $order->load('items.product')
        ]);
    }

    public function update(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'data' => null
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled',
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order Updated Successfully',
            'data' => $order->fresh()->load('items.product')
        ]);
    }
}