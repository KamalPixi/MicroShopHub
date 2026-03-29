<div class="bg-white p-5 rounded-xl border border-gray-200 table-container mx-auto">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            <h3 class="text-base font-bold text-gray-800">Customers</h3>
            <p class="text-xs text-gray-500">Manage customer profiles and order activity.</p>
        </div>
        <div class="text-xs text-gray-500">
            Total: <span class="font-semibold text-gray-700">{{ $customers->total() }}</span>
        </div>
    </div>

    <div class="mb-4">
        <div class="flex items-center justify-between gap-3 mb-3">
            <h4 class="text-sm font-semibold text-gray-700">Today & Growth</h4>
            <p class="text-xs text-gray-500">Customer movement at a glance</p>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
            <div class="rounded-xl border border-gray-200 bg-blue-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-blue-600">New Today</p>
                <p class="mt-1 text-xl font-bold text-blue-800">{{ $stats['new_today'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-cyan-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-cyan-600">New This Month</p>
                <p class="mt-1 text-xl font-bold text-cyan-800">{{ $stats['new_this_month'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-green-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-green-600">Repeat Customers</p>
                <p class="mt-1 text-xl font-bold text-green-800">{{ $stats['repeat_customers'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-amber-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-amber-600">With Orders</p>
                <p class="mt-1 text-xl font-bold text-amber-800">{{ $stats['with_orders'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-red-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-red-600">No Orders Yet</p>
                <p class="mt-1 text-xl font-bold text-red-800">{{ $stats['without_orders'] }}</p>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="flex items-center justify-between gap-3 mb-3">
            <h4 class="text-sm font-semibold text-gray-700">All Time</h4>
            <p class="text-xs text-gray-500">Overall customer base and value</p>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-gray-500">Total Customers</p>
                <p class="mt-1 text-xl font-bold text-gray-900">{{ $stats['total_customers'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-slate-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-slate-600">Total Orders</p>
                <p class="mt-1 text-xl font-bold text-slate-800">{{ $stats['total_orders_all_time'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-indigo-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-indigo-600">Verified Emails</p>
                <p class="mt-1 text-xl font-bold text-indigo-800">{{ $stats['verified_emails'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-green-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-green-600">Lifetime Spend</p>
                @php
                    $currency = \App\Models\Currency::getActive();
                    $symbol = $currency?->symbol ?? '$';
                @endphp
                <p class="mt-1 text-xl font-bold text-green-800">{{ $symbol }}{{ number_format((float) $stats['total_lifetime_spend'], 2) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-amber-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-amber-600">Avg Orders / Customer</p>
                <p class="mt-1 text-xl font-bold text-amber-800">{{ number_format((float) $stats['avg_orders_per_customer'], 2) }}</p>
            </div>
        </div>
    </div>
    
    {{-- success/failed message --}}
    @include('admin.includes.message')

    <div class="mb-4 flex flex-wrap items-center gap-3">
        <div class="flex-1 min-w-[220px]">
            <label for="search" class="block text-xs font-semibold text-gray-600">Search Customers</label>
            <input 
                wire:model.live="search" 
                type="text" 
                id="search" 
                class="input-field mt-1 block w-full border border-gray-300 rounded-lg text-xs px-3 py-2 focus:outline-none focus:ring-0 focus:border-gray-300" 
                placeholder="Search by name, email, or phone"
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

    <div class="overflow-x-auto">
        <table class="table-field w-full text-left text-xs">
            <thead>
                <tr class="bg-gray-50">
                    <th class="font-medium text-gray-700 p-2">Customer</th>
                    <th class="font-medium text-gray-700 p-2">Contact</th>
                    <th class="font-medium text-gray-700 p-2">Default Address</th>
                    <th class="font-medium text-gray-700 p-2">Orders</th>
                    <th class="font-medium text-gray-700 p-2">Joined</th>
                    <th class="font-medium text-gray-700 p-2 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr class="border-t">
                        <td class="p-2">
                            <div class="font-semibold text-gray-800">{{ $customer->name ?? 'Unnamed' }}</div>
                            <div class="text-[11px] text-gray-500">#{{ $customer->id }}</div>
                        </td>
                        <td class="p-2">
                            <div>{{ $customer->email ?? 'N/A' }}</div>
                            <div class="text-[11px] text-gray-500">{{ $customer->phone ?? 'No phone' }}</div>
                        </td>
                        <td class="p-2 text-[11px] text-gray-600">
                            @if($customer->defaultAddress)
                                {{ $customer->defaultAddress->address_line1 ?? '' }}
                                {{ $customer->defaultAddress->address_line2 ? ', '.$customer->defaultAddress->address_line2 : '' }}
                                {{ $customer->defaultAddress->city ? ', '.$customer->defaultAddress->city : '' }}
                                {{ $customer->defaultAddress->postal_code ? ', '.$customer->defaultAddress->postal_code : '' }}
                                {{ $customer->defaultAddress->country ? ', '.$customer->defaultAddress->country : '' }}
                            @else
                                No address
                            @endif
                        </td>
                        <td class="p-2">
                            <div class="font-semibold text-gray-800">{{ $customer->orders_count }}</div>
                            <div class="text-[11px] text-gray-500">Total {{ number_format((float) ($customer->orders_sum_total ?? 0), 2) }}</div>
                        </td>
                        <td class="p-2 text-gray-600">{{ $customer->created_at?->format('M d, Y') }}</td>
                        <td class="p-2 text-end space-x-3">
                            <!-- View -->
                            <a href="{{ route('admin.customers.show', $customer->id) }}" class="admin-action-btn admin-action-view" title="View">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <!-- Edit -->
                            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="admin-action-btn admin-action-edit" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                                </svg>
                            </a>
                            <!-- Delete -->
                            <button 
                                wire:click="deleteCustomer({{ $customer->id }})" 
                                wire:loading.attr="disabled" 
                                onclick="return confirm('Are you sure you want to delete this customer?')"
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
                        <td colspan="7" class="text-center text-gray-500 py-4">No customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-container mt-4">
        {{ $customers->links() }}
    </div>
</div>
