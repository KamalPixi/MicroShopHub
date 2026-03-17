<div class="bg-white p-4 rounded-lg shadow table-container mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Campaigns</h3>
            <p class="text-xs text-gray-500">Create and schedule newsletter campaigns</p>
        </div>
        @if($campaignId)
            <button wire:click="resetForm" class="text-xs font-medium text-gray-600 hover:text-gray-800">Cancel edit</button>
        @endif
    </div>

    @include('admin.includes.message')

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
        <div class="border border-gray-200 rounded-lg p-3">
            <p class="text-xs text-gray-500">Total</p>
            <p class="text-lg font-semibold text-gray-900">{{ $totalCount }}</p>
        </div>
        <div class="border border-gray-200 rounded-lg p-3">
            <p class="text-xs text-gray-500">Scheduled</p>
            <p class="text-lg font-semibold text-amber-600">{{ $scheduledCount }}</p>
        </div>
        <div class="border border-gray-200 rounded-lg p-3">
            <p class="text-xs text-gray-500">Sent</p>
            <p class="text-lg font-semibold text-green-600">{{ $sentCount }}</p>
        </div>
    </div>

    <div class="border border-gray-200 rounded-lg p-4 mb-6 bg-gray-50">
        <h4 class="text-sm font-semibold text-gray-800 mb-3">{{ $campaignId ? 'Edit Campaign' : 'New Campaign' }}</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700">Campaign Name</label>
                <input wire:model.live="name" type="text" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm px-3 py-2">
                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700">Subject</label>
                <input wire:model.live="subject" type="text" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm px-3 py-2">
                @error('subject') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="mt-4">
            <label class="block text-xs font-medium text-gray-700">Content</label>
            <textarea wire:model.live="content" rows="5" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm px-3 py-2"></textarea>
            @error('content') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
                <label class="block text-xs font-medium text-gray-700">Status</label>
                <div class="relative mt-1">
                    <select wire:model.live="status" class="block w-full appearance-none border border-gray-300 rounded-lg shadow-sm text-sm px-3 py-2 bg-white pr-8 focus:outline-none focus:ring-0 focus:border-gray-300">
                        <option value="draft">Draft</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="sent">Sent</option>
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-gray-400">
                        <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </div>
                @error('status') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700">Schedule Time</label>
                <input wire:model.live="scheduled_at" type="datetime-local" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm text-sm px-3 py-1.5 bg-white leading-tight focus:outline-none focus:ring-0 focus:border-gray-300">
                @error('scheduled_at') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-end">
                <button wire:click="save" class="w-full bg-primary text-white text-sm font-medium rounded-lg px-4 py-2 hover:bg-primary">{{ $campaignId ? 'Update Campaign' : 'Create Campaign' }}</button>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="table-field w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="font-medium text-gray-700 p-2">Name</th>
                    <th class="font-medium text-gray-700 p-2">Subject</th>
                    <th class="font-medium text-gray-700 p-2">Status</th>
                    <th class="font-medium text-gray-700 p-2">Scheduled</th>
                    <th class="font-medium text-gray-700 p-2">Created</th>
                    <th class="font-medium text-gray-700 p-2 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($campaigns as $campaign)
                    <tr class="border-t">
                        <td class="p-2">{{ $campaign->name }}</td>
                        <td class="p-2">{{ $campaign->subject }}</td>
                        <td class="p-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                {{ $campaign->status === 'sent' ? 'bg-green-100 text-green-700' : ($campaign->status === 'scheduled' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                                {{ ucfirst($campaign->status) }}
                            </span>
                        </td>
                        <td class="p-2">{{ $campaign->scheduled_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        <td class="p-2">{{ $campaign->created_at?->format('Y-m-d H:i') }}</td>
                        <td class="p-2 text-end space-x-2">
                            <button wire:click="edit({{ $campaign->id }})" class="inline-flex items-center text-xs font-medium text-primary hover:underline">Edit</button>
                            <button wire:click="delete({{ $campaign->id }})" wire:confirm="Delete this campaign?" class="inline-flex items-center text-xs font-medium text-red-600 hover:text-red-700">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-6">No campaigns found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-container mt-4">
        {{ $campaigns->links() }}
    </div>
</div>
