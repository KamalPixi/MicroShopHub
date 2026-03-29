<div class="bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <div class="mx-auto h-12 w-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary text-xl font-bold mb-4">
            S
        </div>
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ __('store.create_account') }}</h2>
        <p class="mt-2 text-sm text-gray-600">{{ __('store.create_account_intro') }}</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-xl shadow-gray-100 sm:rounded-2xl sm:px-10 border border-gray-100">
            @if(!$authSettings['email_password_enabled'])
                <div class="rounded-lg bg-yellow-50 p-4 border border-yellow-200">
                    <p class="text-sm text-yellow-800">{{ __('store.registration_disabled') }}</p>
                </div>
            @else
                <form wire:submit.prevent="register" class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">{{ __('store.full_name') }}</label>
                        <input wire:model="name" type="text" class="mt-1 block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                        @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700">{{ __('store.email_address') }}</label>
                        <input wire:model="email" type="email" autocomplete="email" class="mt-1 block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                        @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700">{{ __('store.password') }}</label>
                        <input wire:model="password" type="password" autocomplete="new-password" class="mt-1 block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                        @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700">{{ __('store.confirm_password') }}</label>
                        <input wire:model="passwordConfirmation" type="password" autocomplete="new-password" class="mt-1 block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                        @error('passwordConfirmation') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-lg shadow-sm text-sm font-bold text-white bg-primary hover:bg-primary">{{ __('store.create_account') }}</button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">{{ __('store.already_have_account') }}</p>
                    <a href="{{ route('login') }}" class="text-sm font-medium text-primary hover:underline">{{ __('store.back_to_login') }}</a>
                </div>
            @endif
        </div>
    </div>
</div>
