<div class="bg-white p-4 rounded-xl border border-gray-200 table-container mx-auto" x-data="{ period: 'today' }">
    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
        <div class="flex items-center gap-3">
            <div>
                <h3 class="text-sm font-bold text-gray-800">Orders</h3>
                <p class="text-[11px] text-gray-500">Showing <span class="font-semibold text-gray-700">{{ $orders->count() }}</span> of <span class="font-semibold text-gray-700">{{ $orders->total() }}</span></p>
            </div>
            <div class="inline-flex rounded-lg border border-gray-200 bg-gray-50 p-0.5 text-[11px] font-semibold">
                <button type="button" @click="period = 'today'" :class="period === 'today' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-500 hover:text-gray-800'" class="rounded-md px-2.5 py-1 transition">
                    Today ({{ $stats['today_total_orders'] }})
                </button>
                <button type="button" @click="period = 'all'" :class="period === 'all' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-500 hover:text-gray-800'" class="rounded-md px-2.5 py-1 transition">
                    All Time ({{ $stats['total_orders'] }})
                </button>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-1 max-w-md justify-end">
            <input 
                wire:model.live="search" 
                type="text" 
                id="search" 
                class="input-field block w-full border border-gray-300 rounded-lg text-xs px-2.5 py-1.5 focus:outline-none focus:ring-0 focus:border-gray-300" 
                placeholder="Search order ID, customer, status..."
            >
            <select wire:model.live="perPage" class="border border-gray-300 rounded-lg text-xs px-2 py-1.5 bg-white shrink-0" title="Per Page">
                <option value="10">10 / pg</option>
                <option value="20">20 / pg</option>
                <option value="30">30 / pg</option>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <div x-show="period === 'today'" class="grid grid-cols-3 lg:grid-cols-6 gap-2">
            <div class="rounded-lg border border-gray-200 bg-slate-50 px-2.5 py-1.5 flex items-center justify-between">
                <span class="text-[11px] text-slate-500">New</span>
                <span class="text-sm font-bold text-slate-900">{{ $stats['today_total_orders'] }}</span>
            </div>
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-1.5 flex items-center justify-between">
                <span class="text-[11px] text-amber-700">Pending Pay</span>
                <span class="text-sm font-bold text-amber-800">{{ $stats['today_pending_payment'] }}</span>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1.5 flex items-center justify-between">
                <span class="text-[11px] text-gray-500">To Process</span>
                <span class="text-sm font-bold text-gray-900">{{ $stats['today_to_process'] }}</span>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1.5 flex items-center justify-between">
                <span class="text-[11px] text-gray-500">Shipped</span>
                <span class="text-sm font-bold text-gray-900">{{ $stats['today_shipped'] }}</span>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1.5 flex items-center justify-between">
                <span class="text-[11px] text-gray-500">Delivered</span>
                <span class="text-sm font-bold text-gray-900">{{ $stats['today_delivered'] }}</span>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1.5 flex items-center justify-between">
                <span class="text-[11px] text-gray-500">Cancelled</span>
                <span class="text-sm font-bold text-gray-900">{{ $stats['today_cancelled'] }}</span>
            </div>
        </div>

        <div x-show="period === 'all'" style="display: none;" class="grid grid-cols-3 lg:grid-cols-6 gap-2">
            <div class="rounded-lg border border-gray-200 bg-slate-50 px-2.5 py-1.5 flex items-center justify-between">
                <span class="text-[11px] text-slate-500">Total</span>
                <span class="text-sm font-bold text-slate-900">{{ $stats['total_orders'] }}</span>
            </div>
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-1.5 flex items-center justify-between">
                <span class="text-[11px] text-amber-700">Pending Pay</span>
                <span class="text-sm font-bold text-amber-800">{{ $stats['pending_payment'] }}</span>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1.5 flex items-center justify-between">
                <span class="text-[11px] text-gray-500">To Process</span>
                <span class="text-sm font-bold text-gray-900">{{ $stats['to_process'] }}</span>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1.5 flex items-center justify-between">
                <span class="text-[11px] text-gray-500">Shipped</span>
                <span class="text-sm font-bold text-gray-900">{{ $stats['shipped'] }}</span>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1.5 flex items-center justify-between">
                <span class="text-[11px] text-gray-500">Delivered</span>
                <span class="text-sm font-bold text-gray-900">{{ $stats['delivered'] }}</span>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1.5 flex items-center justify-between">
                <span class="text-[11px] text-gray-500">Cancelled</span>
                <span class="text-sm font-bold text-gray-900">{{ $stats['cancelled'] }}</span>
            </div>
        </div>
    </div>
    
    {{-- success/failed message --}}
    @include('admin.includes.message')

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
                        <td class="p-2">
                            <div class="text-xs font-medium text-gray-800 leading-tight">{{ $order->created_at?->diffForHumans() }}</div>
                            <div class="text-[11px] text-gray-500 leading-tight">{{ $order->created_at?->format('M d, Y h:i A') }}</div>
                        </td>
                        <td class="p-2 text-end space-x-2" onclick="event.stopPropagation()">
                            <!-- View -->
                            <a href="{{ route('admin.orders.show', ['order' => $order->id]) }}" class="admin-action-btn admin-action-view" title="View">
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
                                class="admin-action-btn admin-action-delete" 
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
