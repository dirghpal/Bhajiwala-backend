<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        return handleApiRequest(function () {

            $totalUsers = User::count();

            $totalProducts = Product::count();

            $totalCategories = Category::count();

            $totalOrders = Order::count();

            $totalSales = Order::where('payment_status', 'paid')
                ->sum('total_amount');

            $pendingOrders = Order::where('status', 'pending')
                ->count();

            $shippedOrders = Order::where('status', 'shipped')
                ->count();

            $deliveredOrders = Order::where('status', 'delivered')
                ->count();

            $this->response['msg'] = 'Dashboard Data';

            $this->response['data'] = [
                'total_users' => $totalUsers,
                'total_products' => $totalProducts,
                'total_categories' => $totalCategories,
                'total_orders' => $totalOrders,
                'total_sales' => $totalSales,
                'pending_orders' => $pendingOrders,
                'shipped_orders' => $shippedOrders,
                'delivered_orders' => $deliveredOrders,
            ];

            return response()->json($this->response);
        });
    }
}