<div class="space-y-6">
    @php
        $activeCurrency = \App\Models\Currency::getActive();
        $currencySymbol = $activeCurrency?->symbol ?? '৳';
        $currencyCode = $activeCurrency?->code ?? 'BDT';
    @endphp
    <!-- Form Section -->
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ $editingId ? 'Edit Coupon' : 'Add Coupon' }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
                <input 
                    wire:model="code" 
                    type="text" 
                    id="code" 
                    class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                    placeholder="Enter coupon code (e.g., SAVE10)"
                >
                @error('code') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                <select 
                    wire:model="type" 
                    id="type" 
                    class="form-input mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2"
                >
                    <option value="percentage">Percentage</option>
                    <option value="fixed">Fixed Amount</option>
                    <option value="free_shipping">Free Shipping</option>
                </select>
                @error('type') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            @if ($type !== 'free_shipping')
                <div>
                        <label for="value" class="block text-sm font-medium text-gray-700">Value {{ $type === 'percentage' ? '(%)' : "({$currencyCode})" }}</label>
                    <input 
                        wire:model="value" 
                        type="number" 
                        id="value" 
                        step="0.01" 
                        class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                    placeholder="Enter coupon value"
                    >
                    @error('value') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
            @endif
            <div>
                <label for="min_order_amount" class="block text-sm font-medium text-gray-700">Minimum Order Amount ({{ $currencyCode }})</label>
                <input 
                    wire:model="min_order_amount" 
                    type="number" 
                    id="min_order_amount" 
                    step="0.01" 
                    class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                    placeholder="Enter minimum order amount"
                >
                @error('min_order_amount') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="usage_limit" class="block text-sm font-medium text-gray-700">Usage Limit (optional)</label>
                <input 
                    wire:model="usage_limit" 
                    type="number" 
                    id="usage_limit" 
                    class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                    placeholder="Enter total usage limit"
                >
                @error('usage_limit') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="per_user_limit" class="block text-sm font-medium text-gray-700">Per User Limit (optional)</label>
                <input 
                    wire:model="per_user_limit" 
                    type="number" 
                    id="per_user_limit" 
                    class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                    placeholder="Enter per user limit"
                >
                @error('per_user_limit') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="starts_at" class="block text-sm font-medium text-gray-700">Starts At (optional)</label>
                <input 
                    wire:model="starts_at" 
                    type="datetime-local" 
                    id="starts_at" 
                    class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2"
                >
                @error('starts_at') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="expires_at" class="block text-sm font-medium text-gray-700">Expires At (optional)</label>
                <input 
                    wire:model="expires_at" 
                    type="datetime-local" 
                    id="expires_at" 
                    class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2"
                >
                @error('expires_at') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Active</label>
                <label class="mt-2 inline-flex items-center gap-2 text-sm text-gray-700">
                    <input 
                        wire:model="active" 
                        type="checkbox" 
                        class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    >
                    <span>Enable this coupon</span>
                </label>
                @error('active') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="md:col-span-2 flex flex-wrap gap-2">
                <button 
                    wire:click="save" 
                    wire:loading.attr="disabled" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm flex items-center"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ $editingId ? 'Update' : 'Save' }}
                </button>
                @if ($editingId)
                    <button 
                        wire:click="resetForm" 
                        class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400 text-sm flex items-center"
                    >
                        Cancel
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- List Section -->
    <div class="bg-white p-4 rounded-lg shadow table-container">
        <div class="flex justify-between">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Coupons List
            </h3>
        </div>
        <div class="mb-4">
            <label for="search" class="block text-sm font-medium text-gray-700">Search Coupons</label>
            <input 
                wire:model.live="search" 
                type="text" 
                id="search" 
                class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                placeholder="Search by coupon code"
            >
        </div>

        {{-- success/failed message --}}
        @include('admin.includes.message')

        <div class="overflow-x-auto">
            <table class="table-field w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="font-medium text-gray-700 p-2">ID</th>
                        <th class="font-medium text-gray-700 p-2">Code</th>
                        <th class="font-medium text-gray-700 p-2">Type</th>
                        <th class="font-medium text-gray-700 p-2">Value</th>
                        <th class="font-medium text-gray-700 p-2">Min Order</th>
                        <th class="font-medium text-gray-700 p-2">Usage Limit</th>
                        <th class="font-medium text-gray-700 p-2">Per User</th>
                        <th class="font-medium text-gray-700 p-2">Starts At</th>
                        <th class="font-medium text-gray-700 p-2">Expires At</th>
                        <th class="font-medium text-gray-700 p-2">Active</th>
                        <th class="font-medium text-gray-700 p-2 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($discounts as $discount)
                        <tr class="border-t">
                            <td class="p-2">{{ $discount->id }}</td>
                            <td class="p-2">{{ $discount->code }}</td>
                            <td class="p-2">
                                @php
                                    $colors = [
                                        'percentage' => 'bg-blue-100 text-blue-800',
                                        'fixed'      => 'bg-green-100 text-green-800',
                                    ];
                                    $colorClass = $colors[$discount->type] ?? 'bg-yellow-100 text-yellow-800';
                                @endphp

                                <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full {{ $colorClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $discount->type)) }}
                                </span>
                            </td>
                            <td class="p-2">
                                {{ $discount->value ? ($discount->type === 'percentage' ? $discount->value . '%' : $currencySymbol . number_format($discount->value, 2)) : 'N/A' }}
                            </td>
                            <td class="p-2">{{ $currencySymbol }}{{ number_format($discount->min_order_amount, 2) }}</td>
                            <td class="p-2">{{ $discount->usage_limit ?? 'Unlimited' }}</td>
                            <td class="p-2">{{ $discount->per_user_limit ?? 'Unlimited' }}</td>
                            <td class="p-2">{{ $discount->starts_at ? $discount->starts_at->format('Y-m-d H:i') : 'N/A' }}</td>
                            <td class="p-2">{{ $discount->expires_at ? $discount->expires_at->format('Y-m-d H:i') : 'N/A' }}</td>
                            <td class="p-2">
                                <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full 
                                    {{ $discount->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $discount->active ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="p-2 text-end space-x-1">
                                <!-- Edit -->
                                <button 
                                    wire:click="edit({{ $discount->id }})" 
                                    class="inline-flex items-center py-1 text-green-600 hover:text-green-800 rounded" 
                                    title="Edit"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                </button>
                                <!-- Delete -->
                                <button 
                                    wire:click="deleteDiscount({{ $discount->id }})" 
                                    wire:loading.attr="disabled" 
                                    wire:confirm="Are you sure you want to delete this coupon?" 
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
                            <td colspan="11" class="text-center text-gray-500 py-4">No coupons found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-container mt-4">
            {{ $discounts->links() }}
        </div>
    </div>
</div>
