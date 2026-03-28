<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-gray-500">Support</p>
            <h2 class="text-lg font-semibold text-gray-900">Contact Messages</h2>
            <p class="text-sm text-gray-500">Messages sent from the storefront contact form.</p>
        </div>
    </div>

    @include('admin.includes.message')

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
        <div class="rounded-xl border border-gray-200 bg-white p-3">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Total</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-3">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">New</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['new'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-3">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Read</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['read'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-3">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Resolved</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['resolved'] }}</p>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-4 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-600">Search</label>
                <input type="text" wire:model.live.debounce.300ms="search" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Name, email, subject, message">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600">Status</label>
                <select wire:model.live="status" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm appearance-none bg-white">
                    <option value="all">All</option>
                    <option value="new">New</option>
                    <option value="read">Read</option>
                    <option value="resolved">Resolved</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600">Per page</label>
                <select wire:model.live="perPage" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm appearance-none bg-white">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600">
                        <th class="px-3 py-2">Message</th>
                        <th class="px-3 py-2">From</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Created</th>
                        <th class="px-3 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $message)
                        <tr class="border-t align-top">
                            <td class="px-3 py-3">
                                <p class="font-semibold text-gray-900">{{ $message->subject }}</p>
                                <p class="mt-1 text-xs text-gray-500 line-clamp-2">{{ $message->message }}</p>
                            </td>
                            <td class="px-3 py-3">
                                <p class="font-medium text-gray-900">{{ $message->name }}</p>
                                <p class="text-xs text-gray-500">{{ $message->email }}</p>
                                @if($message->phone)
                                    <p class="text-xs text-gray-500">{{ $message->phone }}</p>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                                    {{ $message->status === 'resolved' ? 'bg-green-100 text-green-700' : ($message->status === 'read' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ ucfirst($message->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-xs text-gray-500">
                                {{ $message->created_at?->format('d M Y, h:i A') }}
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @if($message->status === 'new')
                                        <button type="button" wire:click="markRead({{ $message->id }})" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:border-primary/25 hover:text-primary">Mark Read</button>
                                    @endif
                                    @if($message->status !== 'resolved')
                                        <button type="button" wire:click="markResolved({{ $message->id }})" class="rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700 hover:bg-green-100">Resolve</button>
                                    @endif
                                    <button type="button" onclick="return confirm('Delete this contact message?')" wire:click="deleteMessage({{ $message->id }})" class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-6 text-center text-gray-500">No contact messages yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $messages->links() }}
        </div>
    </div>
</div>
