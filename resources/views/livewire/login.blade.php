<div class="bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <div class="mx-auto h-12 w-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary text-xl font-bold mb-4">
            S
        </div>
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            Welcome back
        </h2>
        <p class="mt-2 text-sm text-gray-600">
            Sign in or create an account with just your email.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-xl shadow-gray-100 sm:rounded-2xl sm:px-10 border border-gray-100">
            
            @if (session()->has('message'))
                <div class="rounded-lg bg-green-50 p-4 mb-6 flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                    </div>
                </div>
            @endif

            @if(!$otpSent)
                <form wire:submit.prevent="sendOtp" class="space-y-6 animate-fade-in">
                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700">
                            Email address
                        </label>
                        <div class="mt-1">
                            <input wire:model="email" id="email" type="email" autocomplete="email" required 
                                class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm transition-colors">
                        </div>
                        @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <button type="submit" 
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove>Continue with Email</span>
                            <span wire:loading>Sending Code...</span>
                        </button>
                    </div>
                    
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Secure Passwordless Login</span>
                        </div>
                    </div>
                </form>

            @else
                <form wire:submit.prevent="verifyOtp" class="space-y-6 animate-fade-in">
                    <div>
                        <label for="otp" class="block text-sm font-bold text-gray-700 text-center mb-4">
                            Enter the code sent to <span class="text-primary">{{ $email }}</span>
                        </label>
                        <div class="mt-1 flex justify-center">
                            <input wire:model="otp" id="otp" type="text" maxlength="6" autofocus
                                class="block w-48 text-center text-2xl tracking-[0.5em] font-mono border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary py-2"
                                placeholder="······">
                        </div>
                        @error('otp') <p class="text-red-500 text-sm mt-2 text-center font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <button type="submit" 
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove>Verify & Login</span>
                            <span wire:loading>Verifying...</span>
                        </button>
                    </div>

                    <div class="text-center">
                        <button type="button" wire:click="resetInput" class="text-sm font-medium text-gray-500 hover:text-primary transition-colors">
                            Use a different email
                        </button>
                    </div>
                </form>
            @endif
        </div>
        
        <p class="mt-6 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} Your Company. All rights reserved.
        </p>
    </div>
</div>
