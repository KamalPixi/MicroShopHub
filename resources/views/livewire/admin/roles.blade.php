<div class="space-y-6">
    <div class="bg-white p-4 rounded-lg shadow">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1M16 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    {{ $editingId ? 'Edit Role' : 'Add Role' }}
                </h3>
                <p class="text-xs text-gray-500">Roles carry permissions. Admin users are assigned to a role.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Role Name</label>
                <input wire:model="name" type="text" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-primary" placeholder="Editor">
                @error('name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Slug</label>
                <input wire:model="slug" type="text" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-primary" placeholder="editor">
                @error('slug') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2 rounded-xl border border-gray-200 bg-gray-50 p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Permissions</p>
                        <p class="text-xs text-gray-500">Choose access for this role.</p>
                    </div>
                </div>
                <div class="mt-4 max-h-[420px] space-y-4 overflow-y-auto pr-1">
                    @foreach ($permissionGroups as $groupName => $groupPermissions)
                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">{{ $groupName }}</p>
                            <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                @foreach ($groupPermissions as $permissionKey => $label)
                                    <label class="flex items-start gap-3 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">
                                        <input wire:model="permissions" type="checkbox" value="{{ $permissionKey }}" class="mt-0.5 h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                                        <span class="min-w-0">
                                            <span class="block font-medium text-gray-800">{{ $label }}</span>
                                            <span class="block text-[11px] text-gray-500">{{ $permissionKey }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="md:col-span-2 flex flex-wrap gap-2">
                <button type="button" wire:click="save" wire:loading.attr="disabled" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold">
                    {{ $editingId ? 'Update Role' : 'Save Role' }}
                </button>
                @if ($editingId)
                    <button type="button" wire:click="resetForm" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold">
                        Cancel
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow table-container">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h4a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Roles List
                </h3>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Search Roles</label>
            <input wire:model.live="search" type="text" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-primary" placeholder="Search by name or slug">
        </div>

        @include('admin.includes.message')

        <div class="overflow-x-auto">
            <table class="table-field w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="p-2 font-medium text-gray-700">ID</th>
                        <th class="p-2 font-medium text-gray-700">Role</th>
                        <th class="p-2 font-medium text-gray-700">Slug</th>
                        <th class="p-2 font-medium text-gray-700">Admins</th>
                        <th class="p-2 font-medium text-gray-700">Permissions</th>
                        <th class="p-2 font-medium text-gray-700 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr class="border-t">
                            <td class="p-2">{{ $role->id }}</td>
                            <td class="p-2">{{ $role->name }}</td>
                            <td class="p-2"><span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">{{ $role->slug }}</span></td>
                            <td class="p-2">{{ $role->admins_count }}</td>
                            <td class="p-2">{{ count($role->permissions ?? []) }} allowed</td>
                            <td class="p-2 text-end space-x-2">
                                <button wire:click="edit({{ $role->id }})" class="admin-action-btn admin-action-edit" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                </button>
                                <button wire:click="delete({{ $role->id }})" wire:confirm="Delete this role?" class="admin-action-btn admin-action-delete" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m5 0H6" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500">No roles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-container mt-4">
            {{ $roles->links() }}
        </div>
    </div>
</div>
