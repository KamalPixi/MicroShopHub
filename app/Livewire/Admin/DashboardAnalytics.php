<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Order;
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
        $this->totalCustomers = Customer::count();

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
        $this->recentOrders = Order::with('customer')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer_name' => $order->customer ? $order->customer->name : 'Guest',
                    'total' => $order->total,
                    'status' => $order->status,
                    'created_at' => $order->created_at->format('Y-m-d H:i'),
                ];
            });

        // Top Products: Top 5 products by sales (sum of subtotals from order_items)
        $this->topProducts = OrderItem::select('product_id', DB::raw('SUM(subtotal) as total_sales'))
            ->groupBy('product_id')
            ->orderBy('total_sales', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                $product = Product::find($item->product_id);
                return [
                    'name' => $product ? $product->name : 'Unknown',
                    'total_sales' => $item->total_sales,
                ];
            });
    }

    public function render()
    {
        return view('livewire.admin.dashboard-analytics');
    }
}
