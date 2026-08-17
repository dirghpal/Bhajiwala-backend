<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return handleApiRequest(function () {

            $orders = Order::with('user', 'items.product')
                ->latest()
                ->get();

            $this->response['msg'] = 'All Orders';
            $this->response['data'] = $orders;

            return response()->json($this->response);
        });
    }

    public function show(Order $order)
    {
        return handleApiRequest(function () use ($order) {

            $this->response['msg'] = 'Order Details';
            $this->response['data'] =
                $order->load('user', 'items.product');

            return response()->json($this->response);
        });
    }

    public function update(Request $request, Order $order)
    {
        return handleApiRequest(function () use ($request, $order) {

            $request->validate([
                'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled',
            ]);

            $order->update([
                'status' => $request->status,
            ]);

            $this->response['msg'] =
                'Order Status Updated Successfully';

            $this->response['data'] =
                $order->fresh()->load('user', 'items.product');

            return response()->json($this->response);
        });
    }
}