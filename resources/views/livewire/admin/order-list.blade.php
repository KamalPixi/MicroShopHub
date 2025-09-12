<div class="bg-white p-4 rounded-lg shadow table-container mx-auto">
    <div class="flex justify-between">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Order List
        </h3>
    </div>
    
    {{-- success/failed message --}}
    @include('admin.includes.message')

    <div class="mb-4">
        <label for="search" class="block text-sm font-medium text-gray-700">Search Orders</label>
        <input 
            wire:model.live="search" 
            type="text" 
            id="search" 
            class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
            placeholder="Search by order ID, customer name, or status"
        >
    </div>

    <div class="overflow-x-auto">
        <table class="table-field w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="font-medium text-gray-700 p-2">Order ID</th>
                    <th class="font-medium text-gray-700 p-2">Customer</th>
                    <th class="font-medium text-gray-700 p-2">Status</th>
                    <th class="font-medium text-gray-700 p-2">Total</th>
                    <th class="font-medium text-gray-700 p-2">Shipping Cost</th>
                    <th class="font-medium text-gray-700 p-2">Created At</th>
                    <th class="font-medium text-gray-700 p-2 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr class="border-t">
                        <td class="p-2">#{{ $order->id }}</td>
                        <td class="p-2">{{ $order->customer ? $order->customer->name : 'Guest' }}</td>
                        <td class="p-2">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'delivered' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                            @endphp

                            <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full 
                                {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($order->status) }}
                            </span>

                        </td>
                        <td class="p-2">${{ number_format($order->total, 2) }}</td>
                        <td class="p-2">${{ number_format($order->shipping_cost, 2) }}</td>
                        <td class="p-2">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        <td class="p-2 text-end space-x-1">
                            <!-- View -->
                            <a href="{{ route('admin.orders.show', ['order_id' => $order->id]) }}" class="inline-flex items-center py-1 text-blue-600 hover:text-blue-800 rounded" title="View">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <!-- Edit -->
                            <a href="{{ route('admin.orders.edit', ['order_id' => $order->id]) }}" class="inline-flex items-center py-1 text-green-600 hover:text-green-800 rounded" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                                </svg>
                            </a>
                            <!-- Delete -->
                            <button 
                                wire:click="deleteOrder({{ $order->id }})" 
                                wire:loading.attr="disabled" 
                                wire:confirm="Are you sure you want to delete this order?" 
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
                        <td colspan="7" class="text-center text-gray-500 py-4">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-container mt-4">
        {{ $orders->links() }}
    </div>
</div>
