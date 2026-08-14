<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user', 'items.product')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'All Orders',
            'data' => $orders
        ]);
    }

    public function show(Order $order)
    {
        return response()->json([
            'success' => true,
            'message' => 'Order Details',
            'data' => $order->load('user', 'items.product')
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled',
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order Status Updated Successfully',
            'data' => $order->fresh()->load('user', 'items.product')
        ]);
    }
}