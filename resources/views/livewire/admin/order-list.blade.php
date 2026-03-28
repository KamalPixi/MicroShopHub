<div class="bg-white p-5 rounded-xl border border-gray-200 table-container mx-auto">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            <h3 class="text-base font-bold text-gray-800">Orders</h3>
            <p class="text-xs text-gray-500">Track and manage customer orders.</p>
        </div>
        <div class="text-xs text-gray-500">
            Showing <span class="font-semibold text-gray-700">{{ $orders->count() }}</span> of
            <span class="font-semibold text-gray-700">{{ $orders->total() }}</span>
        </div>
    </div>

    <div class="mb-4">
        <div class="flex items-center justify-between gap-3 mb-3">
            <h4 class="text-sm font-semibold text-gray-700">Today</h4>
            <p class="text-xs text-gray-500">What needs attention right now</p>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
            <div class="rounded-xl border border-gray-200 bg-blue-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-blue-600">New Orders</p>
                <p class="mt-1 text-xl font-bold text-blue-800">{{ $stats['today_total_orders'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-amber-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-amber-600">Pending Payment</p>
                <p class="mt-1 text-xl font-bold text-amber-800">{{ $stats['today_pending_payment'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-indigo-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-indigo-600">To Process</p>
                <p class="mt-1 text-xl font-bold text-indigo-800">{{ $stats['today_to_process'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-cyan-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-cyan-600">Shipped</p>
                <p class="mt-1 text-xl font-bold text-cyan-800">{{ $stats['today_shipped'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-green-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-green-600">Delivered</p>
                <p class="mt-1 text-xl font-bold text-green-800">{{ $stats['today_delivered'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-red-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-red-600">Cancelled</p>
                <p class="mt-1 text-xl font-bold text-red-800">{{ $stats['today_cancelled'] }}</p>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="flex items-center justify-between gap-3 mb-3">
            <h4 class="text-sm font-semibold text-gray-700">All Time</h4>
            <p class="text-xs text-gray-500">Overall business volume</p>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-gray-500">Total Orders</p>
                <p class="mt-1 text-xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-amber-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-amber-600">Pending Payment</p>
                <p class="mt-1 text-xl font-bold text-amber-800">{{ $stats['pending_payment'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-indigo-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-indigo-600">To Process</p>
                <p class="mt-1 text-xl font-bold text-indigo-800">{{ $stats['to_process'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-cyan-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-cyan-600">Shipped</p>
                <p class="mt-1 text-xl font-bold text-cyan-800">{{ $stats['shipped'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-green-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-green-600">Delivered</p>
                <p class="mt-1 text-xl font-bold text-green-800">{{ $stats['delivered'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-red-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-red-600">Cancelled / Refunded</p>
                <p class="mt-1 text-xl font-bold text-red-800">{{ $stats['cancelled'] }}</p>
            </div>
        </div>
    </div>
    
    {{-- success/failed message --}}
    @include('admin.includes.message')

    <div class="mb-4 flex flex-wrap items-center gap-3">
        <div class="flex-1 min-w-[220px]">
            <label for="search" class="block text-xs font-semibold text-gray-600">Search Orders</label>
            <input 
                wire:model.live="search" 
                type="text" 
                id="search" 
                class="input-field mt-1 block w-full border border-gray-300 rounded-lg text-xs px-3 py-2 focus:outline-none focus:ring-0 focus:border-gray-300" 
                placeholder="Search by order ID, customer name, or status"
            >
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600">Per Page</label>
            <select wire:model.live="perPage" class="mt-1 border border-gray-300 rounded-lg text-xs px-3 py-2 bg-white">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
            </select>
        </div>
    </div>

    @php
        $defaultCurrency = \App\Models\Currency::getActive();
        $defaultSymbol = $defaultCurrency?->symbol ?? '$';
        $defaultCode = $defaultCurrency?->code ?? 'BDT';
    @endphp
    <div class="overflow-x-auto">
        <table class="table-field w-full text-left text-xs">
            <thead>
                <tr class="bg-gray-50">
                    <th class="font-medium text-gray-700 p-2">Order</th>
                    <th class="font-medium text-gray-700 p-2">Customer</th>
                    <th class="font-medium text-gray-700 p-2">Status</th>
                    <th class="font-medium text-gray-700 p-2">Currency</th>
                    <th class="font-medium text-gray-700 p-2">Total</th>
                    <th class="font-medium text-gray-700 p-2">Shipping Cost</th>
                    <th class="font-medium text-gray-700 p-2">Created At</th>
                    <th class="font-medium text-gray-700 p-2 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr class="border-t hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.orders.show', ['order' => $order->id]) }}'">
                        <td class="p-2">
                            <div class="font-semibold text-gray-800">#{{ $order->order_number ?? $order->id }}</div>
                            <div class="text-[11px] text-gray-500">{{ $order->payment_method ?? '—' }}</div>
                        </td>
                        <td class="p-2">
                            <div class="font-semibold text-gray-800">{{ $order->user ? $order->user->name : 'Guest' }}</div>
                            <div class="text-[11px] text-gray-500">{{ $order->user?->email ?? '—' }}</div>
                        </td>
                        <td class="p-2">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'processing' => 'bg-blue-100 text-blue-800',
                                    'delivered' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                            @endphp

                            <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full 
                                {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($order->status) }}
                            </span>

                        </td>
                        @php
                            $currencySymbol = $order->currency?->symbol ?? '';
                            $currencyCode = $order->currency_code ?? '';
                            $resolvedCode = $currencyCode ?: $defaultCode;
                            $displaySymbol = $currencySymbol ?: ($resolvedCode ? $resolvedCode . ' ' : $defaultSymbol);
                        @endphp
                        <td class="p-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                {{ $resolvedCode ?: 'N/A' }}
                            </span>
                        </td>
                        <td class="p-2">{{ $displaySymbol }}{{ number_format($order->total, 2) }}</td>
                        <td class="p-2">{{ $displaySymbol }}{{ number_format($order->shipping_cost, 2) }}</td>
                        <td class="p-2">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        <td class="p-2 text-end space-x-3" onclick="event.stopPropagation()">
                            <!-- View -->
                            <a href="{{ route('admin.orders.show', ['order' => $order->id]) }}" class="inline-flex items-center py-1 text-primary hover:text-primary rounded" title="View">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <!-- Delete -->
                            <button 
                                wire:click="deleteOrder({{ $order->id }})" 
                                wire:loading.attr="disabled" 
                                onclick="return confirm('Are you sure you want to delete this order?')" 
                                class="inline-flex items-center py-1 text-red-600 hover:text-red-800 rounded" 
                                title="Delete"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m5 0H6" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-gray-500 py-4">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-container mt-4">
        {{ $orders->links() }}
    </div>
</div>
