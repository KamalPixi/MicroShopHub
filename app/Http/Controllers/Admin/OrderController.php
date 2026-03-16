<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index() {
        return view('admin.orders.orders');
    }

    public function show(Order $order) {
        $order->load([
            'user',
            'items.product',
            'items.productVariation',
            'currency',
            'shippingMethod',
            'billingAddress',
            'shippingAddress',
        ]);

        $customerOrderCount = $order->user_id
            ? Order::where('user_id', $order->user_id)->count()
            : 0;

        $customerTotalSpend = $order->user_id
            ? Order::where('user_id', $order->user_id)->sum('total')
            : 0;

        return view('admin.orders.show', [
            'order' => $order,
            'customerOrderCount' => $customerOrderCount,
            'customerTotalSpend' => $customerTotalSpend,
        ]);
    }

}
