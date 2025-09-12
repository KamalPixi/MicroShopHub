<div class="bg-white p-4 rounded-lg shadow table-container mx-auto">
    <div class="flex justify-between">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            Customer List
        </h3>
    </div>
    <div class="mb-4">
        <label for="search" class="block text-sm font-medium text-gray-700">Search Customers</label>
        <input 
            wire:model.live="search" 
            type="text" 
            id="search" 
            class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
            placeholder="Search by name, email, or phone"
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
                    <th class="font-medium text-gray-700 p-2">Email</th>
                    <th class="font-medium text-gray-700 p-2">Phone</th>
                    <th class="font-medium text-gray-700 p-2">Address</th>
                    <th class="font-medium text-gray-700 p-2">Created At</th>
                    <th class="font-medium text-gray-700 p-2 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr class="border-t">
                        <td class="p-2">{{ $customer->id }}</td>
                        <td class="p-2">{{ $customer->name }}</td>
                        <td class="p-2">{{ $customer->email }}</td>
                        <td class="p-2">{{ $customer->phone ?? 'N/A' }}</td>
                        <td class="p-2">{{ $customer->address ?? 'N/A' }}</td>
                        <td class="p-2">{{ $customer->created_at->format('Y-m-d H:i') }}</td>
                        <td class="p-2 text-end space-x-1">
                            <!-- View -->
                            <a href="{{ route('admin.customers.show', ['customer_id' => $customer->id]) }}" class="inline-flex items-center py-1 text-blue-600 hover:text-blue-800 rounded" title="View">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <!-- Edit -->
                            <a href="{{ route('admin.customers.edit', ['customer_id' => $customer->id]) }}" class="inline-flex items-center py-1 text-green-600 hover:text-green-800 rounded" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                                </svg>
                            </a>
                            <!-- Delete -->
                            <button 
                                wire:click="deleteCustomer({{ $customer->id }})" 
                                wire:loading.attr="disabled" 
                                wire:confirm="Are you sure you want to delete this customer?" 
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
