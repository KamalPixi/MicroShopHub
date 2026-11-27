<div class="bg-white p-4 rounded-xl border border-gray-200 table-container mx-auto">
    <div class="flex flex-wrap items-center justify-between gap-2 mb-3 pb-3 border-b border-gray-100">
        <div>
            <h3 class="text-sm font-bold text-gray-800">Newsletter Subscriptions</h3>
            <p class="text-[11px] text-gray-500">Manage subscribers and consent status</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <input
                wire:model.live="search"
                type="text"
                id="search"
                class="border border-gray-300 rounded-lg focus:border-primary focus:ring-primary text-xs px-2.5 py-1.5 w-48"
                placeholder="Search email or name..."
            >
            <select
                wire:model.live="status"
                id="status"
                class="border border-gray-300 rounded-lg text-xs px-2.5 py-1.5 bg-white"
            >
                <option value="">All Statuses</option>
                <option value="subscribed">Subscribed</option>
                <option value="unsubscribed">Unsubscribed</option>
            </select>
            <select
                wire:model.live="perPage"
                id="perPage"
                class="border border-gray-300 rounded-lg text-xs px-2 py-1.5 bg-white"
            >
                <option value="10">10 / pg</option>
                <option value="25">25 / pg</option>
                <option value="50">50 / pg</option>
            </select>
        </div>
    </div>

    @include('admin.includes.message')

    <div class="grid grid-cols-3 gap-2 mb-3">
        <div class="border border-gray-200 bg-gray-50 rounded-lg px-2.5 py-1.5 flex items-center justify-between">
            <p class="text-[11px] text-gray-500">Total</p>
            <p class="text-sm font-bold text-gray-900">{{ $totalCount }}</p>
        </div>
        <div class="border border-green-200 bg-green-50 rounded-lg px-2.5 py-1.5 flex items-center justify-between">
            <p class="text-[11px] text-green-700">Subscribed</p>
            <p class="text-sm font-bold text-green-700">{{ $subscribedCount }}</p>
        </div>
        <div class="border border-gray-200 bg-gray-50 rounded-lg px-2.5 py-1.5 flex items-center justify-between">
            <p class="text-[11px] text-gray-500">Unsubscribed</p>
            <p class="text-sm font-bold text-gray-700">{{ $unsubscribedCount }}</p>
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
