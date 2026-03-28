<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\Currency;
use App\Models\Setting;
use App\Models\SiteAnalyticsPageView;
use App\Models\SiteAnalyticsSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DashboardAnalytics extends Component
{
    public $totalSales;
    public $totalOrders;
    public $ordersToday;
    public $ordersThisMonth;
    public $totalCustomers;
    public $totalProducts;
    public $averageOrderValue;
    public $pendingOrders;
    public $revenueThisMonth;
    public $revenueToday;
    public $revenueThisWeek;
    public $activeShippingMethods;
    public $recentOrders;
    public $topProducts;
    public $newCustomersThisMonth;
    public $lowStockProducts;
    public $outOfStockCount;
    public $recentProducts;
    public $currencySymbol;
    public $shopName;
    public $siteVisitors = 0;
    public $siteSessions = 0;
    public $sitePageViews = 0;
    public $siteBounceRate = 0;
    public $topBrowsers = [];
    public $topReferrers = [];
    public $mostVisitedPages = [];

    public function mount()
    {
        $this->currencySymbol = Currency::getActive()->symbol;
        $settings = Setting::whereIn('key', ['shop_name'])->pluck('value', 'key');
        $this->shopName = $settings['shop_name'] ?: config('app.name', 'Store Name');
        // Total Sales: Sum of all order totals
        $this->totalSales = Order::sum('total');

        // Total Orders
        $this->totalOrders = Order::count();
        $this->ordersToday = Order::whereDate('created_at', Carbon::today())->count();
        $this->ordersThisMonth = Order::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->count();

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

        // Revenue Today
        $this->revenueToday = Order::whereDate('created_at', Carbon::today())->sum('total');

        // Revenue This Week
        $this->revenueThisWeek = Order::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->sum('total');

        // Active Shipping Methods
        $this->activeShippingMethods = ShippingMethod::where('active', true)->count();

        // New Customers This Month
        $this->newCustomersThisMonth = User::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->count();

        // Low Stock Products (<= 5, > 0)
        $this->lowStockProducts = Product::where('stock', '>', 0)
            ->where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        // Out of Stock Count
        $this->outOfStockCount = Product::where('stock', '<=', 0)->count();

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
                    'created_at_human' => $order->created_at?->diffForHumans(),
                    'created_at_time' => $order->created_at?->format('d M Y, h:i A'),
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

        // Recent Products
        $this->recentProducts = Product::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        if (Schema::hasTable('site_analytics_sessions') && Schema::hasTable('site_analytics_page_views')) {
            $this->siteSessions = SiteAnalyticsSession::count();
            $this->siteVisitors = SiteAnalyticsSession::query()->distinct()->count('visitor_token');
            $this->sitePageViews = SiteAnalyticsPageView::count();
            $bouncedSessions = SiteAnalyticsSession::where('page_views_count', 1)->count();
            $this->siteBounceRate = $this->siteSessions > 0 ? round(($bouncedSessions / $this->siteSessions) * 100, 1) : 0;

            $this->topBrowsers = SiteAnalyticsSession::query()
                ->select('browser', DB::raw('COUNT(*) as total_sessions'))
                ->groupBy('browser')
                ->orderByDesc('total_sessions')
                ->take(5)
                ->get()
                ->map(function ($row) {
                    return [
                        'label' => $row->browser ?: 'Unknown',
                        'total_sessions' => (int) $row->total_sessions,
                    ];
                });

            $referrerSourceSql = "CASE WHEN referrer_host IS NULL OR referrer_host = '' THEN 'Direct / None' ELSE referrer_host END";
            $this->topReferrers = SiteAnalyticsPageView::query()
                ->selectRaw("{$referrerSourceSql} as source, COUNT(*) as total_views")
                ->groupByRaw($referrerSourceSql)
                ->orderByDesc('total_views')
                ->take(5)
                ->get()
                ->map(function ($row) {
                    return [
                        'label' => $row->source,
                        'total_views' => (int) $row->total_views,
                    ];
                });

            $this->mostVisitedPages = SiteAnalyticsPageView::query()
                ->select('page_title', 'page_path', DB::raw('COUNT(*) as total_views'))
                ->groupBy('page_title', 'page_path')
                ->orderByDesc('total_views')
                ->take(5)
                ->get()
                ->map(function ($row) {
                    return [
                        'label' => $row->page_title ?: $row->page_path,
                        'path' => $row->page_path,
                        'total_views' => (int) $row->total_views,
                    ];
                });
        }
    }

    public function render()
    {
        return view('livewire.admin.dashboard-analytics');
    }

    public function clearSiteAnalytics(): void
    {
        if (! Schema::hasTable('site_analytics_sessions') || ! Schema::hasTable('site_analytics_page_views')) {
            session()->flash('message', 'Analytics tables are not available yet.');
            return;
        }

        SiteAnalyticsPageView::query()->delete();
        SiteAnalyticsSession::query()->delete();

        $this->siteVisitors = 0;
        $this->siteSessions = 0;
        $this->sitePageViews = 0;
        $this->siteBounceRate = 0;
        $this->topBrowsers = [];
        $this->topReferrers = [];
        $this->mostVisitedPages = [];

        session()->flash('message', 'Site analytics cleared successfully.');
    }
}
