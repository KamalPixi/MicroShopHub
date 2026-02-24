<div class="bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <div class="mx-auto h-12 w-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary text-xl font-bold mb-4">
            S
        </div>
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Customer Login</h2>
        <p class="mt-2 text-sm text-gray-600">Sign in to your account to manage orders and faster checkout.</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-xl shadow-gray-100 sm:rounded-2xl sm:px-10 border border-gray-100">
            @if (session()->has('message'))
                <div class="rounded-lg bg-green-50 p-4 mb-6">
                    <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                </div>
            @endif

            @if(!$authSettings['email_password_enabled'] && !$authSettings['email_otp_enabled'])
                <div class="rounded-lg bg-yellow-50 p-4 border border-yellow-200">
                    <p class="text-sm text-yellow-800">Customer login is disabled by the admin right now.</p>
                </div>
            @else
                @if(!$showForgotPassword)
                    <div class="space-y-6">
                        @if($authSettings['email_password_enabled'] && $authSettings['email_otp_enabled'])
                            <div class="grid grid-cols-2 gap-2 rounded-lg bg-gray-100 p-1">
                                <button type="button" wire:click="setMethod('password')" class="py-2 text-sm rounded-md font-semibold {{ $activeMethod === 'password' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600' }}">Email + Password</button>
                                <button type="button" wire:click="setMethod('otp')" class="py-2 text-sm rounded-md font-semibold {{ $activeMethod === 'otp' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600' }}">Email OTP</button>
                            </div>
                        @endif

                        <div>
                            <label for="email" class="block text-sm font-bold text-gray-700">Email address</label>
                            <input wire:model="email" id="email" type="email" autocomplete="email" class="mt-1 block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                            @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        @if($activeMethod === 'password' && $authSettings['email_password_enabled'])
                            <form wire:submit.prevent="loginWithPassword" class="space-y-4">
                                <div>
                                    <label for="password" class="block text-sm font-bold text-gray-700">Password</label>
                                    <input wire:model="password" id="password" type="password" autocomplete="current-password" class="mt-1 block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                                    @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="flex items-center justify-between">
                                    <label class="inline-flex items-center text-sm text-gray-600">
                                        <input wire:model="remember" type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary mr-2">
                                        Remember me
                                    </label>
                                    <button type="button" wire:click="showForgotPasswordForm" class="text-sm font-medium text-primary hover:underline">Forgot password?</button>
                                </div>

                                <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-lg shadow-sm text-sm font-bold text-white bg-primary hover:bg-blue-700">Sign In</button>
                            </form>
                        @endif

                        @if($activeMethod === 'otp' && $authSettings['email_otp_enabled'])
                            @if(!$otpSent)
                                <button type="button" wire:click="sendOtp" class="w-full flex justify-center py-3 px-4 rounded-lg shadow-sm text-sm font-bold text-white bg-primary hover:bg-blue-700">Send Login Code</button>
                            @else
                                <form wire:submit.prevent="verifyOtp" class="space-y-4">
                                    <div>
                                        <label for="otp" class="block text-sm font-bold text-gray-700">6-digit code</label>
                                        <input wire:model="otp" id="otp" type="text" maxlength="6" class="mt-1 block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm text-center tracking-[0.4em] font-mono focus:outline-none focus:ring-primary focus:border-primary">
                                        @error('otp') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-lg shadow-sm text-sm font-bold text-white bg-primary hover:bg-blue-700">Verify & Login</button>
                                    <button type="button" wire:click="resetLoginState" class="w-full text-sm font-medium text-gray-500 hover:text-primary">Resend / Change Email</button>
                                </form>
                            @endif
                        @endif
                    </div>
                @else
                    <div class="space-y-4">
                        <h3 class="text-lg font-bold text-gray-900">Reset Password</h3>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Email</label>
                            <input wire:model="resetEmail" type="email" class="mt-1 block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                            @error('resetEmail') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        @if(!$resetOtpSent)
                            <button type="button" wire:click="sendPasswordResetOtp" class="w-full flex justify-center py-3 px-4 rounded-lg shadow-sm text-sm font-bold text-white bg-primary hover:bg-blue-700">Send Reset Code</button>
                        @else
                            <div>
                                <label class="block text-sm font-bold text-gray-700">Reset code</label>
                                <input wire:model="resetOtp" type="text" maxlength="6" class="mt-1 block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm text-center tracking-[0.4em] font-mono focus:outline-none focus:ring-primary focus:border-primary">
                                @error('resetOtp') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700">New password</label>
                                <input wire:model="newPassword" type="password" class="mt-1 block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                                @error('newPassword') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700">Confirm password</label>
                                <input wire:model="newPasswordConfirmation" type="password" class="mt-1 block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                                @error('newPasswordConfirmation') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <button type="button" wire:click="resetPassword" class="w-full flex justify-center py-3 px-4 rounded-lg shadow-sm text-sm font-bold text-white bg-primary hover:bg-blue-700">Update Password</button>
                        @endif

                        <button type="button" wire:click="hideForgotPasswordForm" class="w-full text-sm font-medium text-gray-500 hover:text-primary">Back to Login</button>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
