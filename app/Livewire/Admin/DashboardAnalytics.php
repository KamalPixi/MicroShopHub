<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingMethod;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardAnalytics extends Component
{
    public $totalSales;
    public $totalOrders;
    public $totalCustomers;
    public $totalProducts;
    public $averageOrderValue;
    public $pendingOrders;
    public $revenueThisMonth;
    public $activeShippingMethods;
    public $recentOrders;
    public $topProducts;

    public function mount()
    {
        // Total Sales: Sum of all order totals
        $this->totalSales = Order::sum('total');

        // Total Orders
        $this->totalOrders = Order::count();

        // Total Customers
        $this->totalCustomers = User::count();

        // Total Products
        $this->totalProducts = Product::count();

        // Average Order Value
        $this->averageOrderValue = $this->totalOrders > 0 ? $this->totalSales / $this->totalOrders : 0;

        // Pending Orders
        $this->pendingOrders = Order::where('status', 'pending')->count();

        // Revenue This Month
        $this->revenueThisMonth = Order::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->sum('total');

        // Active Shipping Methods
        $this->activeShippingMethods = ShippingMethod::where('active', true)->count();

        // Recent Orders: Last 5 orders with customer name, total, status
        $this->recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer_name' => $order->user ? $order->user->name : 'Guest',
                    'total' => $order->total,
                    'status' => $order->status,
                    'created_at' => $order->created_at->format('Y-m-d H:i'),
                ];
            });

        // Top Products: Top 5 products by sales (sum of subtotals from order_items)
        $this->topProducts = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.name', 
                DB::raw('SUM(order_items.quantity * order_items.price) as total_sales')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sales', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'total_sales' => (float) $item->total_sales,
                ];
            });
            }

    public function render()
    {
        return view('livewire.admin.dashboard-analytics');
    }
}
