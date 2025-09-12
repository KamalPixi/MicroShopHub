<div class="space-y-6">
    <!-- Analytics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-lg shadow flex items-center">
            <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h3 class="text-sm font-medium text-gray-600">Total Sales</h3>
                <p class="text-xl font-bold text-gray-800">${{ number_format($totalSales, 2) }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow flex items-center">
            <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <div>
                <h3 class="text-sm font-medium text-gray-600">Total Orders</h3>
                <p class="text-xl font-bold text-gray-800">{{ $totalOrders }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow flex items-center">
            <svg class="w-8 h-8 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <div>
                <h3 class="text-sm font-medium text-gray-600">Total Customers</h3>
                <p class="text-xl font-bold text-gray-800">{{ $totalCustomers }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow flex items-center">
            <svg class="w-8 h-8 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <div>
                <h3 class="text-sm font-medium text-gray-600">Total Products</h3>
                <p class="text-xl font-bold text-gray-800">{{ $totalProducts }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow flex items-center">
            <svg class="w-8 h-8 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"></path>
            </svg>
            <div>
                <h3 class="text-sm font-medium text-gray-600">Average Order Value</h3>
                <p class="text-xl font-bold text-gray-800">${{ number_format($averageOrderValue, 2) }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow flex items-center">
            <svg class="w-8 h-8 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h3 class="text-sm font-medium text-gray-600">Pending Orders</h3>
                <p class="text-xl font-bold text-gray-800">{{ $pendingOrders }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow flex items-center">
            <svg class="w-8 h-8 text-teal-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <div>
                <h3 class="text-sm font-medium text-gray-600">Revenue This Month</h3>
                <p class="text-xl font-bold text-gray-800">${{ number_format($revenueThisMonth, 2) }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow flex items-center">
            <svg class="w-8 h-8 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0v6a2 2 0 01-.586 1.414L12 18l-8.414-4.586A2 2 0 013 12V7m16 0l-8 4-8-4"></path>
            </svg>
            <div>
                <h3 class="text-sm font-medium text-gray-600">Active Shipping Methods</h3>
                <p class="text-xl font-bold text-gray-800">{{ $activeShippingMethods }}</p>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            Recent Orders
        </h3>
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="text-gray-600">
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
                        <td class="p-2">${{ number_format($order['total'], 2) }}</td>
                        <td class="p-2">
                            <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full 
                                {{ 
                                    $order['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                    ($order['status'] === 'delivered' ? 'bg-green-100 text-green-800' : 
                                    ($order['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))
                                }}">
                                {{ ucfirst($order['status']) }}
                            </span>
                        </td>
                        <td class="p-2">{{ $order['created_at'] }}</td>
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

    <!-- Top Products Table -->
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18m-7 5h7"></path>
            </svg>
            Top Products
        </h3>
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="text-gray-600">
                    <th class="p-2">Product Name</th>
                    <th class="p-2">Total Sales</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($topProducts as $product)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-2">{{ $product['name'] }}</td>
                        <td class="p-2">${{ number_format($product['total_sales'], 2) }}</td>
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
