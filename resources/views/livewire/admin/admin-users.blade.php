<div class="space-y-6">
    <!-- Form Section -->
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            {{ $editingId ? 'Edit Admin' : 'Add Admin' }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input 
                    wire:model="name" 
                    type="text" 
                    id="name" 
                    class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                    placeholder="Enter admin name"
                >
                @error('name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input 
                    wire:model="email" 
                    type="email" 
                    id="email" 
                    class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                    placeholder="Enter admin email"
                >
                @error('email') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            @if (!$editingId)
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input 
                        wire:model="password" 
                        type="password" 
                        id="password" 
                        class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                        placeholder="Enter password"
                    >
                    @error('password') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
            @endif
            <div>
                <label for="role_id" class="block text-sm font-medium text-gray-700">Role</label>
                <select 
                    wire:model="role_id" 
                    id="role_id" 
                    class="form-input mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2"
                >
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Admins List
            </h3>
        </div>
        <div class="mb-4">
            <label for="search" class="block text-sm font-medium text-gray-700">Search Admins</label>
            <input 
                wire:model.live="search" 
                type="text" 
                id="search" 
                class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                placeholder="Search by name or email"
            >
        </div>

        {{-- success/failed message --}}
        @include('admin.includes.message')

        <div class="overflow-x-auto">
            <table class="table-field w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="font-medium text-gray-700 p-2">ID</th>
                        <th class="font-medium text-gray-700 p-2">Name</th>
                        <th class="font-medium text-gray-700 p-2">Email</th>
                        <th class="font-medium text-gray-700 p-2">Role</th>
                        <th class="font-medium text-gray-700 p-2">Role Permissions</th>
                        <th class="font-medium text-gray-700 p-2 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($admins as $admin)
                        <tr class="border-t">
                            <td class="p-2">{{ $admin->id }}</td>
                            <td class="p-2">{{ $admin->name }}</td>
                            <td class="p-2">{{ $admin->email }}</td>
                            <td class="p-2">
                                <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-700">
                                    {{ $admin->role_label }}
                                </span>

                            </td>
                            <td class="p-2">
                                @if ($admin->roleSlug() === 'super_admin')
                                    <span class="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">All access</span>
                                @else
                                    <span class="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">{{ count($admin->effectivePermissions()) }} allowed</span>
                                @endif
                            </td>
                            <td class="p-2 text-end space-x-1">
                                <!-- Edit -->
                                <button 
                                    wire:click="edit({{ $admin->id }})" 
                                    class="inline-flex items-center py-1 text-green-600 hover:text-green-800 rounded" 
                                    title="Edit"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                </button>
                                <!-- Delete -->
                                <button 
                                    wire:click="deleteAdmin({{ $admin->id }})" 
                                    wire:loading.attr="disabled" 
                                    wire:confirm="Are you sure you want to delete this admin?" 
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
                            <td colspan="6" class="text-center text-gray-500 py-4">No admins found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-container mt-4">
            {{ $admins->links() }}
        </div>
    </div>
</div>
