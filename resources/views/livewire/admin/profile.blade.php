<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-1 space-y-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary text-white text-xl font-bold">
                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-500">Admin Profile</p>
                    <h2 class="mt-1 text-xl font-semibold text-gray-900">{{ $admin->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $admin->email }}</p>
                </div>
            </div>

            <div class="mt-5 grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                    <p class="text-[11px] uppercase tracking-[0.18em] text-gray-500">Role</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ $admin->role_label }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                    <p class="text-[11px] uppercase tracking-[0.18em] text-gray-500">Role Permissions</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ count($permissions) }} available</p>
                </div>
            </div>

            <div class="mt-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Role access summary</p>
                <div class="mt-3 space-y-3">
                    @foreach ($permissionGroups as $groupName => $groupPermissions)
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-gray-500">{{ $groupName }}</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($groupPermissions as $permissionKey => $label)
                                    @php $enabled = in_array($permissionKey, $permissions, true); @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $enabled ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $label }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Update Profile</h3>
                    <p class="text-sm text-gray-500">Update your name, email, or password.</p>
                </div>
            </div>

            @include('admin.includes.message')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input wire:model="name" type="text" class="mt-1 block w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-primary focus:ring-primary" placeholder="Your name">
                    @error('name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input wire:model="email" type="email" class="mt-1 block w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-primary focus:ring-primary" placeholder="Your email">
                    @error('email') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Current Password</label>
                    <input wire:model="current_password" type="password" class="mt-1 block w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-primary focus:ring-primary" placeholder="Required if changing password">
                    @error('current_password') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">New Password</label>
                    <input wire:model="new_password" type="password" class="mt-1 block w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-primary focus:ring-primary" placeholder="Leave empty to keep current password">
                    @error('new_password') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input wire:model="new_password_confirmation" type="password" class="mt-1 block w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-primary focus:ring-primary" placeholder="Repeat new password">
                </div>
                <div class="flex items-end">
                    <button type="button" wire:click="save" wire:loading.attr="disabled" class="inline-flex items-center rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary/90">
                        Save Profile
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
