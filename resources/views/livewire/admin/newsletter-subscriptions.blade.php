<div class="bg-white p-4 rounded-lg shadow table-container mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Newsletter Subscriptions</h3>
            <p class="text-xs text-gray-500">Manage subscribers and consent status</p>
        </div>
    </div>

    @include('admin.includes.message')

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
        <div class="border border-gray-200 rounded-lg p-3">
            <p class="text-xs text-gray-500">Total</p>
            <p class="text-lg font-semibold text-gray-900">{{ $totalCount }}</p>
        </div>
        <div class="border border-gray-200 rounded-lg p-3">
            <p class="text-xs text-gray-500">Subscribed</p>
            <p class="text-lg font-semibold text-green-600">{{ $subscribedCount }}</p>
        </div>
        <div class="border border-gray-200 rounded-lg p-3">
            <p class="text-xs text-gray-500">Unsubscribed</p>
            <p class="text-lg font-semibold text-gray-600">{{ $unsubscribedCount }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
        <div>
            <label for="search" class="block text-xs font-medium text-gray-700">Search</label>
            <input
                wire:model.live="search"
                type="text"
                id="search"
                class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm px-3 py-2"
                placeholder="Search by email or name"
            >
        </div>
        <div>
            <label for="status" class="block text-xs font-medium text-gray-700">Status</label>
            <select
                wire:model.live="status"
                id="status"
                class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm px-3 py-2"
            >
                <option value="">All</option>
                <option value="subscribed">Subscribed</option>
                <option value="unsubscribed">Unsubscribed</option>
            </select>
        </div>
        <div>
            <label for="perPage" class="block text-xs font-medium text-gray-700">Per page</label>
            <select
                wire:model.live="perPage"
                id="perPage"
                class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm px-3 py-2"
            >
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="table-field w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="font-medium text-gray-700 p-2">Email</th>
                    <th class="font-medium text-gray-700 p-2">Name</th>
                    <th class="font-medium text-gray-700 p-2">Status</th>
                    <th class="font-medium text-gray-700 p-2">Subscribed</th>
                    <th class="font-medium text-gray-700 p-2">Source</th>
                    <th class="font-medium text-gray-700 p-2 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subscriptions as $subscription)
                    <tr class="border-t">
                        <td class="p-2">{{ $subscription->email }}</td>
                        <td class="p-2">{{ $subscription->name ?? '—' }}</td>
                        <td class="p-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $subscription->status === 'subscribed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </td>
                        <td class="p-2">{{ $subscription->subscribed_at?->format('Y-m-d H:i') ?? $subscription->created_at?->format('Y-m-d H:i') }}</td>
                        <td class="p-2">{{ $subscription->source ?? '—' }}</td>
                        <td class="p-2 text-end space-x-2">
                            <button
                                wire:click="toggleStatus({{ $subscription->id }})"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center text-xs font-medium {{ $subscription->status === 'subscribed' ? 'text-gray-600 hover:text-gray-800' : 'text-green-600 hover:text-green-700' }}"
                                title="Toggle status"
                            >
                                {{ $subscription->status === 'subscribed' ? 'Unsubscribe' : 'Resubscribe' }}
                            </button>
                            <button
                                wire:click="deleteSubscription({{ $subscription->id }})"
                                wire:loading.attr="disabled"
                                wire:confirm="Delete this subscription?"
                                class="inline-flex items-center text-xs font-medium text-red-600 hover:text-red-700"
                                title="Delete"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-6">No subscriptions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-container mt-4">
        {{ $subscriptions->links() }}
    </div>
</div>
