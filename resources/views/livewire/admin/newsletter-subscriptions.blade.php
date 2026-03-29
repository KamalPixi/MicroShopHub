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
            <div class="relative mt-1">
                <select
                    wire:model.live="status"
                    id="status"
                    class="block w-full appearance-none border border-gray-300 rounded-lg shadow-sm text-sm px-3 py-2 bg-white pr-8 focus:outline-none focus:ring-0 focus:border-gray-300"
                >
                    <option value="">All</option>
                    <option value="subscribed">Subscribed</option>
                    <option value="unsubscribed">Unsubscribed</option>
                </select>
                <span class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-gray-400">
                    <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                    </svg>
                </span>
            </div>
        </div>
        <div>
            <label for="perPage" class="block text-xs font-medium text-gray-700">Per page</label>
            <div class="relative mt-1">
                <select
                    wire:model.live="perPage"
                    id="perPage"
                    class="block w-full appearance-none border border-gray-300 rounded-lg shadow-sm text-sm px-3 py-2 bg-white pr-8 focus:outline-none focus:ring-0 focus:border-gray-300"
                >
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-gray-400">
                    <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                    </svg>
                </span>
            </div>
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
                                class="admin-action-btn {{ $subscription->status === 'subscribed' ? 'admin-action-warning' : 'admin-action-success' }}"
                                title="Toggle status"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16M13 5l7 7-7 7" />
                                </svg>
                            </button>
                            <button
                                wire:click="deleteSubscription({{ $subscription->id }})"
                                wire:loading.attr="disabled"
                                wire:confirm="Delete this subscription?"
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
