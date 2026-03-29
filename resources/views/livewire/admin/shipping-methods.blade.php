<div class="space-y-6">
    <!-- Form Section -->
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0v6a2 2 0 01-.586 1.414L12 18l-8.414-4.586A2 2 0 013 12V7m16 0l-8 4-8-4"></path>
            </svg>
            {{ $editingId ? 'Edit Shipping Method' : 'Add Shipping Method' }}
        </h3>
        
        <div>
            {{-- success/failed message --}}
            @include('admin.includes.message')
            @include('admin.includes.errors')
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input 
                    wire:model="name" 
                    type="text" 
                    id="name" 
                    class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                    placeholder="Enter shipping method name"
                >
                @error('name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="cost" class="block text-sm font-medium text-gray-700">Cost ($)</label>
                <input 
                    wire:model="cost" 
                    type="number" 
                    step="0.01" 
                    id="cost" 
                    class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                    placeholder="Enter cost"
                >
                @error('cost') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="estimated_days" class="block text-sm font-medium text-gray-700">Estimated Days (optional)</label>
                <input 
                    wire:model="estimated_days" 
                    type="number" 
                    id="estimated_days" 
                    class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                    placeholder="Enter estimated delivery days"
                >
                @error('estimated_days') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Active</label>
                <label class="mt-2 inline-flex items-center gap-2 text-sm text-gray-700">
                    <input 
                        wire:model="active" 
                        type="checkbox" 
                        class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    >
                    <span>Enable this shipping method</span>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0v6a2 2 0 01-.586 1.414L12 18l-8.414-4.586A2 2 0 013 12V7m16 0l-8 4-8-4"></path>
                </svg>
                Shipping Methods List
            </h3>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-gray-500">Total Methods</p>
                <p class="mt-1 text-xl font-bold text-gray-900">{{ $stats['total_methods'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-green-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-green-600">Active Methods</p>
                <p class="mt-1 text-xl font-bold text-green-800">{{ $stats['active_methods'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-blue-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-blue-600">Used in Orders</p>
                <p class="mt-1 text-xl font-bold text-blue-800">{{ $stats['used_methods'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-amber-50 px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-amber-600">Unused Methods</p>
                <p class="mt-1 text-xl font-bold text-amber-800">{{ $stats['unused_methods'] }}</p>
            </div>
        </div>
        <div class="mb-4 rounded-xl border border-gray-200 bg-slate-50 px-4 py-3">
            <p class="text-[11px] uppercase tracking-wide text-slate-500">Most Used Method</p>
            <p class="mt-1 text-sm font-semibold text-gray-800">
                {{ $stats['most_used_method_name'] ?? 'No orders yet' }}
                @if($stats['most_used_method_name'])
                    <span class="text-gray-500 font-normal">- {{ $stats['most_used_method_count'] }} orders</span>
                @endif
            </p>
            <p class="mt-1 text-xs text-gray-500">
                {{ $stats['total_orders_using_shipping'] }} orders have selected a shipping method so far.
            </p>
        </div>
        <div class="mb-4">
            <label for="search" class="block text-sm font-medium text-gray-700">Search Shipping Methods</label>
            <input 
                wire:model.live="search" 
                type="text" 
                id="search" 
                class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                placeholder="Search by method name"
            >
        </div>

        @if (session()->has('message'))
            <div class="mb-4 p-2 bg-green-100 text-green-700 rounded-md text-sm">
                {{ session('message') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="table-field w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="font-medium text-gray-700 p-2">ID</th>
                        <th class="font-medium text-gray-700 p-2">Name</th>
                        <th class="font-medium text-gray-700 p-2">Cost</th>
                        <th class="font-medium text-gray-700 p-2">Estimated Days</th>
                        <th class="font-medium text-gray-700 p-2">Used in Orders</th>
                        <th class="font-medium text-gray-700 p-2">Active</th>
                        <th class="font-medium text-gray-700 p-2 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($shippingMethods as $method)
                        <tr class="border-t">
                            <td class="p-2">{{ $method->id }}</td>
                            <td class="p-2">{{ $method->name }}</td>
                            <td class="p-2">${{ number_format($method->cost, 2) }}</td>
                            <td class="p-2">{{ $method->estimated_days ?? 'N/A' }}</td>
                            <td class="p-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ $method->orders_count ?? 0 }} orders
                                </span>
                            </td>
                            <td class="p-2">
                                <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full 
                                    {{ $method->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $method->active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="p-2 text-end space-x-1">
                                <!-- Edit -->
                                <button 
                                    wire:click="edit({{ $method->id }})" 
                                    class="admin-action-btn admin-action-edit" 
                                    title="Edit"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                </button>
                                <!-- Delete -->
                                <button 
                                    wire:click="deleteShippingMethod({{ $method->id }})" 
                                    wire:loading.attr="disabled" 
                                    wire:confirm="Are you sure you want to delete this shipping method?" 
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
                            <td colspan="7" class="text-center text-gray-500 py-4">No shipping methods found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-container mt-4">
            {{ $shippingMethods->links() }}
        </div>
    </div>
</div>
