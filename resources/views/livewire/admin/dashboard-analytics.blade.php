<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-gray-500">Admin Dashboard</p>
            <h2 class="text-lg font-semibold text-gray-900">{{ $shopName }}</h2>
            <p class="text-sm text-gray-500">{{ $shopName }} overview of sales, orders, customers, and inventory.</p>
        </div>
    </div>
    <!-- Analytics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex items-start gap-3">
            <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <h3 class="text-xs font-medium text-gray-600">Total Sales</h3>
                <p class="text-lg font-bold leading-tight text-gray-800">{{ $currencySymbol }}{{ number_format($totalSales, 2) }}</p>
                <p class="text-[11px] leading-tight text-gray-500">Today: {{ $currencySymbol }}{{ number_format($revenueToday, 2) }}</p>
                <p class="text-[11px] leading-tight text-gray-500">This Month: {{ $currencySymbol }}{{ number_format($revenueThisMonth, 2) }}</p>
            </div>
        </div>
        <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex items-start gap-3">
            <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <h3 class="text-xs font-medium text-gray-600">Total Orders</h3>
                <p class="text-lg font-bold leading-tight text-gray-800">{{ $totalOrders }}</p>
                <p class="text-[11px] leading-tight text-gray-500">Today: {{ $ordersToday }}</p>
                <p class="text-[11px] leading-tight text-gray-500">This Month: {{ $ordersThisMonth }}</p>
            </div>
        </div>
        <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex items-start gap-3">
            <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <h3 class="text-xs font-medium text-gray-600">Total Customers</h3>
                <p class="text-lg font-bold leading-tight text-gray-800">{{ $totalCustomers }}</p>
                <p class="text-[11px] leading-tight text-gray-500">New This Month: {{ $newCustomersThisMonth }}</p>
            </div>
        </div>
        <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex items-start gap-3">
            <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-yellow-50 text-yellow-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <h3 class="text-xs font-medium text-gray-600">Total Products</h3>
                <p class="text-lg font-bold leading-tight text-gray-800">{{ $totalProducts }}</p>
                <p class="text-[11px] leading-tight text-gray-500">Low Stock: {{ $lowStockProducts->count() }}</p>
            </div>
        </div>
        <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex items-start gap-3">
            <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <h3 class="text-xs font-medium text-gray-600">Average Order Value</h3>
                <p class="text-lg font-bold leading-tight text-gray-800">{{ $currencySymbol }}{{ number_format($averageOrderValue, 2) }}</p>
            </div>
        </div>
        <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex items-start gap-3">
            <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <h3 class="text-xs font-medium text-gray-600">Pending Orders</h3>
                <p class="text-lg font-bold leading-tight text-gray-800">{{ $pendingOrders }}</p>
            </div>
        </div>
        <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex items-start gap-3">
            <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-teal-50 text-teal-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <h3 class="text-xs font-medium text-gray-600">Revenue This Month</h3>
                <p class="text-lg font-bold leading-tight text-gray-800">{{ $currencySymbol }}{{ number_format($revenueThisMonth, 2) }}</p>
            </div>
        </div>
        <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex items-start gap-3">
            <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <h3 class="text-xs font-medium text-gray-600">Revenue Today</h3>
                <p class="text-lg font-bold leading-tight text-gray-800">{{ $currencySymbol }}{{ number_format($revenueToday, 2) }}</p>
            </div>
        </div>
        <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex items-start gap-3">
            <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-cyan-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <h3 class="text-xs font-medium text-gray-600">Revenue This Week</h3>
                <p class="text-lg font-bold leading-tight text-gray-800">{{ $currencySymbol }}{{ number_format($revenueThisWeek, 2) }}</p>
            </div>
        </div>
        <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex items-start gap-3">
            <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0v6a2 2 0 01-.586 1.414L12 18l-8.414-4.586A2 2 0 013 12V7m16 0l-8 4-8-4"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <h3 class="text-xs font-medium text-gray-600">Active Shipping Methods</h3>
                <p class="text-lg font-bold leading-tight text-gray-800">{{ $activeShippingMethods }}</p>
            </div>
        </div>
        {{-- <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex flex-col items-start gap-2">
            <svg class="w-6 h-6 text-emerald-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <div>
                <h3 class="text-sm font-medium text-gray-600">New Customers (Month)</h3>
                <p class="text-xl font-bold text-gray-800">{{ $newCustomersThisMonth }}</p>
            </div>
        </div> --}}
        <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex items-start gap-3">
            <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <h3 class="text-xs font-medium text-gray-600">Low Stock Items</h3>
                <p class="text-lg font-bold leading-tight text-gray-800">{{ $lowStockProducts->count() }}</p>
            </div>
        </div>
        <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex items-start gap-3">
            <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <h3 class="text-xs font-medium text-gray-600">Out of Stock</h3>
                <p class="text-lg font-bold leading-tight text-gray-800">{{ $outOfStockCount }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Recent Orders Table -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                Recent Orders
            </h3>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-gray-600 bg-gray-50">
                        <th class="p-2">Order ID</th>
                        <th class="p-2">Customer</th>
                        <th class="p-2">Total</th>
                        <th class="p-2">Status</th>
                        <th class="p-2">Created At</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($recentOrders as $order)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="p-2">#{{ $order['id'] }}</td>
                            <td class="p-2">{{ $order['customer_name'] }}</td>
                        <td class="p-2">{{ $currencySymbol }}{{ number_format($order['total'], 2) }}</td>
                            <td class="p-2">
                                <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full 
                                    {{ 
                                        $order['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                        ($order['status'] === 'processing' ? 'bg-blue-100 text-blue-800' : 
                                        ($order['status'] === 'delivered' ? 'bg-green-100 text-green-800' : 
                                        ($order['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')))
                                    }}">
                                    {{ ucfirst($order['status']) }}
                                </span>
                            </td>
                            <td class="p-2">
                                <div class="text-xs font-medium leading-tight text-gray-800">{{ $order['created_at_human'] }}</div>
                                <div class="text-[10px] leading-tight text-gray-500">{{ $order['created_at_time'] }}</div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($recentOrders->isEmpty())
                        <tr>
                            <td colspan="5" class="p-2 text-center text-gray-500">No recent orders.</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Products Table -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18m-7 5h7"></path>
                </svg>
                Top Products
            </h3>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-gray-600 bg-gray-50">
                        <th class="p-2">Product Name</th>
                        <th class="p-2">Total Sales</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($topProducts as $product)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="p-2">{{ $product['name'] }}</td>
                        <td class="p-2">{{ $currencySymbol }}{{ number_format($product['total_sales'], 2) }}</td>
                        </tr>
                    @endforeach
                    @if ($topProducts->isEmpty())
                        <tr>
                            <td colspan="2" class="p-2 text-center text-gray-500">No top products.</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Inventory Alerts -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Inventory Alerts
            </h3>
            <div class="text-xs text-gray-500 mb-2">Low stock (≤ 5)</div>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-gray-600 bg-gray-50">
                        <th class="p-2">Product</th>
                        <th class="p-2">Stock</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($lowStockProducts as $product)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="p-2">{{ $product->name }}</td>
                            <td class="p-2">{{ $product->stock }}</td>
                        </tr>
                    @endforeach
                    @if ($lowStockProducts->isEmpty())
                        <tr>
                            <td colspan="2" class="p-2 text-center text-gray-500">No low stock items.</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="mt-3 text-xs text-gray-600">Out of stock: <span class="font-semibold text-gray-900">{{ $outOfStockCount }}</span></div>
        </div>

        <!-- Recent Products -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"></path>
                </svg>
                Recent Products
            </h3>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-gray-600 bg-gray-50">
                        <th class="p-2">Product</th>
                        <th class="p-2">Price</th>
                        <th class="p-2">Stock</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($recentProducts as $product)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="p-2">{{ $product->name }}</td>
                            <td class="p-2">{{ $currencySymbol }}{{ number_format($product->price, 2) }}</td>
                            <td class="p-2">{{ $product->stock }}</td>
                        </tr>
                    @endforeach
                    @if ($recentProducts->isEmpty())
                        <tr>
                            <td colspan="3" class="p-2 text-center text-gray-500">No recent products.</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm space-y-3">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-semibold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13l4-4 4 4 4-4 4 4 2-2"></path>
                    </svg>
                    Site Analytics
                </h3>
                <p class="text-xs text-gray-500">Traffic, referrers, and browsing behavior</p>
            </div>
            <button
                type="button"
                wire:click="clearSiteAnalytics"
                onclick="return confirm('Clear all site analytics data?')"
                class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100"
            >
                Clear Analytics
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Sessions</p>
                <p class="mt-1.5 text-xl font-bold text-gray-900">{{ number_format($siteSessions) }}</p>
                <p class="text-[11px] text-gray-500 mt-0.5">All tracked visits</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Visitors</p>
                <p class="mt-1.5 text-xl font-bold text-gray-900">{{ number_format($siteVisitors) }}</p>
                <p class="text-[11px] text-gray-500 mt-0.5">Unique browser ids</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Page Views</p>
                <p class="mt-1.5 text-xl font-bold text-gray-900">{{ number_format($sitePageViews) }}</p>
                <p class="text-[11px] text-gray-500 mt-0.5">All page loads</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Bounce Rate</p>
                <p class="mt-1.5 text-xl font-bold text-gray-900">{{ number_format($siteBounceRate, 1) }}%</p>
                <p class="text-[11px] text-gray-500 mt-0.5">Sessions with one page view</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-3">
            <div class="rounded-xl border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-3 py-2.5">
                    <h4 class="text-sm font-semibold text-gray-800">Top Browsers</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-gray-600">
                                <th class="px-3 py-2">Browser</th>
                                <th class="px-3 py-2">Sessions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topBrowsers as $browser)
                                <tr class="border-t">
                                    <td class="px-3 py-2 text-gray-700">{{ $browser['label'] }}</td>
                                    <td class="px-3 py-2 font-semibold text-gray-900">{{ number_format($browser['total_sessions']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-3 py-2 text-center text-gray-500">No analytics data yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-3 py-2.5">
                    <h4 class="text-sm font-semibold text-gray-800">Top Referrers</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-gray-600">
                                <th class="px-3 py-2">Source</th>
                                <th class="px-3 py-2">Views</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topReferrers as $referrer)
                                <tr class="border-t">
                                    <td class="px-3 py-2 text-gray-700 break-all">{{ $referrer['label'] }}</td>
                                    <td class="px-3 py-2 font-semibold text-gray-900">{{ number_format($referrer['total_views']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-3 py-2 text-center text-gray-500">No analytics data yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-3 py-2.5">
                    <h4 class="text-sm font-semibold text-gray-800">Most Visited Pages</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-gray-600">
                                <th class="px-3 py-2">Page</th>
                                <th class="px-3 py-2">Views</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mostVisitedPages as $page)
                                <tr class="border-t">
                                    <td class="px-3 py-2">
                                        <div class="font-medium text-gray-900">{{ $page['label'] }}</div>
                                        <div class="text-[11px] text-gray-500 break-all">{{ $page['path'] }}</div>
                                    </td>
                                    <td class="px-3 py-2 font-semibold text-gray-900">{{ number_format($page['total_views']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-3 py-2 text-center text-gray-500">No analytics data yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
