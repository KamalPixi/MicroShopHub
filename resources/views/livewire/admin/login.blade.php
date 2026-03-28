<div>
    <form wire:submit.prevent="login" class="space-y-4">
        <div wire:loading wire:target="login">
            @include('admin.includes.loading')
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                </svg>
                Email Address
            </label>
            <input type="email" wire:model="email" class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none" placeholder="admin@example.com" required>
            @error('email')
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.104-.896-2-2-2s-2 .896-2 2 2 4 2 4 2-2.896 2-4zm0 0c0-1.104-.896-2-2-2s-2 .896-2 2m2 2v4m-6-6H4m16 0h-4"></path>
                </svg>
                Password
            </label>
            <input type="password" wire:model="password" class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none" placeholder="••••••••" required>
            @error('password')
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
            @enderror
            <p class="mt-1 text-xs text-gray-500">After 5 wrong attempts, login is locked for 1 hour.</p>
        </div>

        <div class="flex items-center justify-between">
            <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" wire:model="remember" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span>Remember me</span>
            </label>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center justify-center">
            Sign In
        </button>
    </form>
</div>
