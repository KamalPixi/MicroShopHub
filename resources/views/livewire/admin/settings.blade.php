<div class="bg-white p-6 rounded-lg shadow table-container mx-auto">
    <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
        <h3 class="text-xl font-bold text-gray-800 flex items-center">
            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            Store Configuration
        </h3>
    </div>

    @include('admin.includes.message')

    <div class="space-y-6">
        <section class="rounded-xl border border-gray-200 p-5 bg-white">
            <h4 class="text-base font-bold text-gray-800">General Identity</h4>
            <p class="text-xs text-gray-500 mt-1 mb-4">Configure your store's basic information and branding appearance.</p>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="shop_name" class="block text-sm font-semibold text-gray-700">Shop Name</label>
                        <input wire:model="settings.shop_name" type="text" id="shop_name" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="e.g. My Awesome Store">
                        @error('settings.shop_name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="site_title" class="block text-sm font-semibold text-gray-700">Site Title</label>
                        <input wire:model="settings.site_title" type="text" id="site_title" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="e.g. Best Deals Online">
                        @error('settings.site_title') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label for="logo" class="block text-sm font-semibold text-gray-700">Store Logo</label>
                    <div class="flex items-center space-x-4 mt-2">
                        @if ($settings['shop_logo'])
                            <div class="p-1 border border-gray-200 rounded">
                                <img src="{{ Storage::url($settings['shop_logo']) }}" alt="Logo" class="h-12 w-auto object-contain">
                            </div>
                        @endif
                        <input wire:model="logo" type="file" id="logo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    @error('logo') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="branding_color" class="block text-sm font-semibold text-gray-700">Primary Color</label>
                        <div class="flex items-center mt-1">
                            <input wire:model="settings.branding_color" type="color" id="branding_color" class="h-9 w-12 border border-gray-300 rounded p-0.5 cursor-pointer">
                            <input wire:model="settings.branding_color" type="text" class="ml-2 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 font-mono uppercase" placeholder="#000000">
                        </div>
                    </div>
                    <div>
                        <label for="secondary_color" class="block text-sm font-semibold text-gray-700">Secondary Color</label>
                        <div class="flex items-center mt-1">
                            <input wire:model="settings.secondary_color" type="color" id="secondary_color" class="h-9 w-12 border border-gray-300 rounded p-0.5 cursor-pointer">
                            <input wire:model="settings.secondary_color" type="text" class="ml-2 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 font-mono uppercase" placeholder="#6B7280">
                        </div>
                    </div>
                    <div>
                        <label for="accent_color" class="block text-sm font-semibold text-gray-700">Accent Color</label>
                        <div class="flex items-center mt-1">
                            <input wire:model="settings.accent_color" type="color" id="accent_color" class="h-9 w-12 border border-gray-300 rounded p-0.5 cursor-pointer">
                            <input wire:model="settings.accent_color" type="text" class="ml-2 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 font-mono uppercase" placeholder="#F59E0B">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 flex items-center justify-end gap-3">
                @if($savedSection === 'general')
                    <span class="text-xs font-semibold text-green-600">Saved</span>
                @endif
                <button
                    wire:click="saveGeneral"
                    wire:loading.attr="disabled"
                    class="bg-primary hover:bg-primary text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-sm transition"
                >
                    Save General
                </button>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 p-5 bg-white">
            <h4 class="text-base font-bold text-gray-800">SEO & Metadata</h4>
            <p class="text-xs text-gray-500 mt-1 mb-4">Optimize your store for search engines.</p>

            <div class="space-y-4">
                <div>
                    <label for="meta_description" class="block text-sm font-semibold text-gray-700">Meta Description</label>
                    <textarea wire:model="settings.meta_description" id="meta_description" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" rows="3"></textarea>
                </div>
                <div>
                    <label for="meta_keywords" class="block text-sm font-semibold text-gray-700">Meta Keywords</label>
                    <input wire:model="settings.meta_keywords" type="text" id="meta_keywords" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2">
                </div>
            </div>

            <div class="mt-5 flex items-center justify-end gap-3">
                @if($savedSection === 'seo')
                    <span class="text-xs font-semibold text-green-600">Saved</span>
                @endif
                <button
                    wire:click="saveSeo"
                    wire:loading.attr="disabled"
                    class="bg-primary hover:bg-primary text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-sm transition"
                >
                    Save SEO
                </button>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 p-5 bg-white">
            <h4 class="text-base font-bold text-gray-800">Customer Authentication</h4>
            <p class="text-xs text-gray-500 mt-1 mb-4">Enable or disable customer login methods and guest ordering.</p>

            <div class="bg-gray-50 p-5 rounded-lg border border-gray-200 space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Email + Password Login</p>
                        <p class="text-xs text-gray-500">Allow customers to sign in using email and password.</p>
                    </div>
                    <input wire:model="settings.customer_auth_email_password_enabled" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-1">
                </div>

                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Email OTP Login</p>
                        <p class="text-xs text-gray-500">Allow customers to login via one-time code sent to email.</p>
                    </div>
                    <input wire:model="settings.customer_auth_email_otp_enabled" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-1">
                </div>

                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Guest Ordering</p>
                        <p class="text-xs text-gray-500">Allow checkout without creating/signing into an account.</p>
                    </div>
                    <input wire:model="settings.customer_auth_guest_checkout_enabled" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-1">
                </div>
            </div>

            @error('settings.customer_auth_email_password_enabled')
                <span class="text-red-600 text-xs block mt-2">{{ $message }}</span>
            @enderror

            <div class="mt-5 flex items-center justify-end gap-3">
                @if($savedSection === 'auth')
                    <span class="text-xs font-semibold text-green-600">Saved</span>
                @endif
                <button
                    wire:click="saveAuth"
                    wire:loading.attr="disabled"
                    class="bg-primary hover:bg-primary text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-sm transition"
                >
                    Save Authentication
                </button>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 p-5 bg-white">
            <h4 class="text-base font-bold text-gray-800">Payment Gateways</h4>
            <p class="text-xs text-gray-500 mt-1 mb-4">Configure credentials and labels. Leave keys blank to disable a method.</p>

            <div class="space-y-5">
                <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                    <div class="flex items-center mb-4 justify-between">
                        <span class="font-bold text-gray-800 text-sm">Cash on Delivery</span>
                        <svg class="h-6 w-auto text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Gateway Display Name</label>
                            <input wire:model="settings.cod_label" type="text" class="input-field block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="Cash on Delivery / Pay on Arrival">
                        </div>

                        <div class="flex items-center pt-2">
                            <input wire:model="settings.cod_enabled" type="checkbox" id="cod_enabled" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="cod_enabled" class="ml-2 text-sm font-medium text-gray-700 select-none cursor-pointer">Enable Cash on Delivery</label>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-end gap-3">
                        @if($savedSection === 'cod')
                            <span class="text-xs font-semibold text-green-600">Saved</span>
                        @endif
                        <button wire:click="saveCodGateway" type="button" class="bg-primary hover:bg-primary text-white rounded-lg px-3 py-2 text-xs font-semibold">
                            Save COD
                        </button>
                    </div>
                </div>

                <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                    <div class="flex items-center mb-4 justify-between">
                        <div class="flex items-center">
                            <span class="font-bold text-gray-800 text-sm">SSLCommerz</span>
                            <span class="ml-2 px-2 py-0.5 rounded text-[10px] font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">Local (BDT)</span>
                        </div>
                        <img src="https://securepay.sslcommerz.com/public/image/sslcommerz.png" class="h-6 opacity-90" alt="SSL">
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Gateway Display Name</label>
                            <input wire:model="settings.sslcommerz_label" type="text" class="input-field block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="bKash / Nagad / Rocket / Visa / MasterCard">
                            <p class="text-[10px] text-gray-400 mt-1">This text appears on the checkout page.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Store ID</label>
                                <input wire:model="settings.sslcommerz_store_id" type="text" class="input-field block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 font-mono" placeholder="your_store_id">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Store Password</label>
                                <input wire:model="settings.sslcommerz_api_key" type="password" class="input-field block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 font-mono" placeholder="••••••••">
                            </div>
                        </div>

                        <div class="flex items-center pt-2">
                            <input wire:model="settings.sslcommerz_sandbox" type="checkbox" id="ssl_sandbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="ssl_sandbox" class="ml-2 text-sm font-medium text-gray-700 select-none cursor-pointer">Enable Sandbox Mode (Test Mode)</label>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-end gap-3">
                        @if($savedSection === 'sslcommerz')
                            <span class="text-xs font-semibold text-green-600">Saved</span>
                        @endif
                        <button wire:click="saveSslCommerzGateway" type="button" class="bg-primary hover:bg-primary text-white rounded-lg px-3 py-2 text-xs font-semibold">
                            Save SSLCommerz
                        </button>
                    </div>
                </div>

                <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                    <div class="flex items-center mb-4 justify-between">
                        <span class="font-bold text-gray-800 text-sm">Stripe</span>
                        <svg class="h-6 w-auto text-blue-600" viewBox="0 0 40 17" fill="currentColor"><path d="M4.64 16.56h-4.64v-16.56h4.64v16.56zm9.24-11.23c-2.5 0-4.32 1.95-4.32 4.67s1.82 4.67 4.32 4.67 4.32-1.95 4.32-4.67-1.82-4.67-4.32-4.67zm0 7.64c-1.57 0-2.67-1.32-2.67-2.97s1.1-2.97 2.67-2.97 2.67 1.32 2.67 2.97-1.1 2.97-2.67 2.97zm8.4-7.64h-1.6v11.23h1.6v-4.82c0-2.3.9-3.2 2.65-3.2v-1.63c-1.25 0-2.22.53-2.65 1.48v-3.06zm8.17 0c-2.5 0-4.32 1.95-4.32 4.67s1.82 4.67 4.32 4.67 4.32-1.95 4.32-4.67-1.82-4.67-4.32-4.67zm0 7.64c-1.57 0-2.67-1.32-2.67-2.97s1.1-2.97 2.67-2.97 2.67 1.32 2.67 2.97-1.1 2.97-2.67 2.97zm5.95 3.59h1.6v-15.17h-1.6v15.17zm6.75-12.87c.92 0 1.62-.7 1.62-1.62s-.7-1.62-1.62-1.62-1.62.7-1.62 1.62.7 1.62 1.62 1.62zm-.8 1.64h1.6v11.23h-1.6v-11.23z"/></svg>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Gateway Display Name</label>
                            <input wire:model="settings.stripe_label" type="text" class="input-field block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="Credit / Debit Card (International)">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Secret Key</label>
                            <input wire:model="settings.stripe_api_key" type="password" class="input-field block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 font-mono" placeholder="sk_live_...">
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-end gap-3">
                        @if($savedSection === 'stripe')
                            <span class="text-xs font-semibold text-green-600">Saved</span>
                        @endif
                        <button wire:click="saveStripeGateway" type="button" class="bg-primary hover:bg-primary text-white rounded-lg px-3 py-2 text-xs font-semibold">
                            Save Stripe
                        </button>
                    </div>
                </div>

                <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                    <div class="flex items-center mb-4 justify-between">
                        <div class="flex items-center">
                            <span class="font-bold text-gray-800 text-sm">bKash</span>
                            <span class="ml-2 px-2 py-0.5 rounded text-[10px] font-bold bg-pink-100 text-pink-700 border border-pink-200">BDT</span>
                        </div>
                        <span class="text-xs text-pink-600 font-semibold">Checkout URL</span>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Base URL</label>
                            <input wire:model="settings.bkash_base_url" type="text" class="input-field block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="https://tokenized.sandbox.bka.sh/v1.2.0-beta">
                            <p class="text-[10px] text-gray-400 mt-1">Use sandbox or production base URL from bKash docs.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">App Key</label>
                                <input wire:model="settings.bkash_app_key" type="text" class="input-field block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 font-mono" placeholder="app_key">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">App Secret</label>
                                <input wire:model="settings.bkash_app_secret" type="password" class="input-field block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 font-mono" placeholder="app_secret">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Username</label>
                                <input wire:model="settings.bkash_username" type="text" class="input-field block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 font-mono" placeholder="username">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password</label>
                                <input wire:model="settings.bkash_password" type="password" class="input-field block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 font-mono" placeholder="password">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-end gap-3">
                        @if($savedSection === 'bkash')
                            <span class="text-xs font-semibold text-green-600">Saved</span>
                        @endif
                        <button wire:click="saveBkashGateway" type="button" class="bg-primary hover:bg-primary text-white rounded-lg px-3 py-2 text-xs font-semibold">
                            Save bKash
                        </button>
                    </div>
                </div>

                <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                    <div class="flex items-center mb-4 justify-between">
                        <span class="font-bold text-gray-800 text-sm">PayPal</span>
                        <svg class="h-5 w-auto text-blue-800" viewBox="0 0 18 18" fill="currentColor"><path d="M15.3 5.4c-.6-2.4-3-3.6-6.6-3.6h-4.2c-.3 0-.6.3-.6.6v12.6c0 .3.3.6.6.6h2.1c.3 0 .6-.3.6-.6v-2.4h1.2c3.9 0 6.6-1.5 6.9-6.6zM6.6 3.6h1.8c2.4 0 3.9.6 4.2 2.1.3 1.5-.6 3-3.6 3h-2.4V3.6z"/></svg>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Gateway Display Name</label>
                            <input wire:model="settings.paypal_label" type="text" class="input-field block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="Pay via PayPal">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Client Secret</label>
                            <input wire:model="settings.paypal_api_key" type="password" class="input-field block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 font-mono" placeholder="PayPal Secret Key">
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-end gap-3">
                        @if($savedSection === 'paypal')
                            <span class="text-xs font-semibold text-green-600">Saved</span>
                        @endif
                        <button wire:click="savePaypalGateway" type="button" class="bg-primary hover:bg-primary text-white rounded-lg px-3 py-2 text-xs font-semibold">
                            Save PayPal
                        </button>
                    </div>
                </div>
            </div>

        </section>

        <section class="rounded-xl border border-gray-200 p-5 bg-white">
            <h4 class="text-base font-bold text-gray-800">Store Operations</h4>
            <p class="text-xs text-gray-500 mt-1 mb-4">Currency and tax configurations.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="currency" class="block text-sm font-semibold text-gray-700">Default Currency</label>
                    <select wire:model="settings.currency" id="currency" class="form-input mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2">
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->code }}">{{ $currency->code }} ({{ $currency->symbol }})</option>
                        @endforeach
                    </select>
                    @error('settings.currency') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="tax_rate" class="block text-sm font-semibold text-gray-700">Tax Rate (%)</label>
                    <div class="relative mt-1">
                        <input wire:model="settings.tax_rate" type="number" id="tax_rate" class="input-field block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 pr-8" placeholder="0">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 rounded-lg border border-dashed border-gray-300 p-4 bg-gray-50">
                <h5 class="text-sm font-bold text-gray-800">Add Currency</h5>
                <p class="text-xs text-gray-500 mt-1 mb-3">Add a new currency code and symbol to use in this store.</p>

                <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700">Code</label>
                        <input wire:model="newCurrency.code" type="text" maxlength="3" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase" placeholder="AED">
                        @error('newCurrency.code') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700">Name</label>
                        <input wire:model="newCurrency.name" type="text" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="UAE Dirham">
                        @error('newCurrency.name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700">Symbol</label>
                        <input wire:model="newCurrency.symbol" type="text" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="AED">
                        @error('newCurrency.symbol') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700">Rate</label>
                        <input wire:model="newCurrency.exchange_rate" type="number" step="0.0001" min="0.0001" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="1.0000">
                        @error('newCurrency.exchange_rate') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex items-end">
                        <button wire:click="addCurrency" type="button" class="w-full bg-secondary text-white rounded-lg px-3 py-2 text-sm font-semibold hover:bg-secondary">
                            Add Currency
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-5 rounded-lg border border-dashed border-gray-300 p-4 bg-gray-50">
                <h5 class="text-sm font-bold text-gray-800">Supported Countries</h5>
                <p class="text-xs text-gray-500 mt-1 mb-3">These countries appear in checkout address selection.</p>

                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($countries as $country)
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-md bg-white border border-gray-200 text-xs font-semibold text-gray-700">
                            {{ $country->name }} ({{ $country->code }})
                            <button type="button" wire:click="removeCountry('{{ $country->code }}')" class="text-red-500 hover:text-red-700">x</button>
                        </span>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700">Country Code</label>
                        <input wire:model="newCountry.code" type="text" maxlength="2" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase" placeholder="AE">
                        @error('newCountry.code') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700">Country Name</label>
                        <input wire:model="newCountry.name" type="text" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="United Arab Emirates">
                        @error('newCountry.name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex items-end">
                        <button wire:click="addCountry" type="button" class="w-full bg-secondary text-white rounded-lg px-3 py-2 text-sm font-semibold hover:bg-secondary">
                            Add Country
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-5 flex items-center justify-end gap-3">
                @if($savedSection === 'operations')
                    <span class="text-xs font-semibold text-green-600">Saved</span>
                @endif
                <button
                    wire:click="saveOperations"
                    wire:loading.attr="disabled"
                    class="bg-primary hover:bg-primary text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-sm transition"
                >
                    Save Operations
                </button>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 p-5 bg-white">
            <h4 class="text-base font-bold text-gray-800">Social & Contact</h4>
            <p class="text-xs text-gray-500 mt-1 mb-4">Customer support info and social media links.</p>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700">Support Email</label>
                        <input wire:model="settings.email" type="email" id="email" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm px-3 py-2" placeholder="support@example.com">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-700">Support Phone</label>
                        <input wire:model="settings.phone" type="text" id="phone" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm px-3 py-2" placeholder="+1 234 567 890">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Social Profiles (Full URLs)</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-xs">FB</span>
                            <input wire:model="settings.social_facebook" type="url" class="focus:ring-primary focus:border-primary block w-full rounded-none rounded-r-md sm:text-sm border-gray-300 px-3 py-2" placeholder="https://facebook.com/...">
                        </div>
                        <div class="flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-xs">TW</span>
                            <input wire:model="settings.social_twitter" type="url" class="focus:ring-primary focus:border-primary block w-full rounded-none rounded-r-md sm:text-sm border-gray-300 px-3 py-2" placeholder="https://twitter.com/...">
                        </div>
                        <div class="flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-xs">IG</span>
                            <input wire:model="settings.social_instagram" type="url" class="focus:ring-primary focus:border-primary block w-full rounded-none rounded-r-md sm:text-sm border-gray-300 px-3 py-2" placeholder="https://instagram.com/...">
                        </div>
                        <div class="flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-xs">IN</span>
                            <input wire:model="settings.social_linkedin" type="url" class="focus:ring-primary focus:border-primary block w-full rounded-none rounded-r-md sm:text-sm border-gray-300 px-3 py-2" placeholder="https://linkedin.com/in/...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 flex items-center justify-end gap-3">
                @if($savedSection === 'social')
                    <span class="text-xs font-semibold text-green-600">Saved</span>
                @endif
                <button
                    wire:click="saveSocial"
                    wire:loading.attr="disabled"
                    class="bg-primary hover:bg-primary text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-sm transition"
                >
                    Save Contact & Social
                </button>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 p-5 bg-white">
            <h4 class="text-base font-bold text-gray-800">Email Settings</h4>
            <p class="text-xs text-gray-500 mt-1 mb-4">Configure SMTP details used across the system for all email sending.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700">SMTP Host</label>
                    <input wire:model="settings.mail_host" type="text" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm px-3 py-2" placeholder="smtp.example.com">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">SMTP Port</label>
                    <input wire:model="settings.mail_port" type="number" min="1" max="65535" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm px-3 py-2" placeholder="587">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">SMTP Username</label>
                    <input wire:model="settings.mail_username" type="text" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm px-3 py-2" placeholder="user@example.com">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">SMTP Password</label>
                    <input wire:model="settings.mail_password" type="password" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm px-3 py-2" placeholder="••••••••">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Encryption</label>
                    <select wire:model="settings.mail_encryption" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm px-3 py-2">
                        <option value="tls">TLS</option>
                        <option value="ssl">SSL</option>
                        <option value="none">None</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">From Name</label>
                    <input wire:model="settings.mail_from_name" type="text" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm px-3 py-2" placeholder="ShopHub">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700">From Address</label>
                    <input wire:model="settings.mail_from_address" type="email" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm px-3 py-2" placeholder="no-reply@example.com">
                </div>
            </div>

            <div class="mt-5 flex items-center justify-end gap-3">
                @if($savedSection === 'email')
                    <span class="text-xs font-semibold text-green-600">Saved</span>
                @endif
                <button
                    wire:click="saveEmailSettings"
                    wire:loading.attr="disabled"
                    class="bg-primary hover:bg-primary text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-sm transition"
                >
                    Save Email Settings
                </button>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 p-5 bg-white">
            <h4 class="text-base font-bold text-gray-800">Admin Notifications</h4>
            <p class="text-xs text-gray-500 mt-1 mb-4">Notify admins when new orders are placed.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start gap-3">
                    <input type="checkbox" wire:model="settings.admin_notify_email_enabled" class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary mt-1">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Email Notifications</p>
                        <p class="text-xs text-gray-500">Send a summary email on each new order.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <input type="checkbox" wire:model="settings.admin_notify_telegram_enabled" class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary mt-1">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Telegram Notifications</p>
                        <p class="text-xs text-gray-500">Send a message to a Telegram chat.</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h5 class="text-sm font-semibold text-gray-800">Email Config</h5>
                        <span class="text-[10px] text-gray-500">Admin alerts</span>
                    </div>
                    <label class="block text-xs font-semibold text-gray-600">Admin Email</label>
                    <input wire:model="settings.admin_notify_email_address" type="email" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm px-3 py-2" placeholder="admin@example.com">
                </div>

                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h5 class="text-sm font-semibold text-gray-800">Telegram Config</h5>
                        <span class="text-[10px] text-gray-500">Bot & chat</span>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600">Bot Token</label>
                            <input wire:model="settings.admin_telegram_bot_token" type="text" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm px-3 py-2" placeholder="123456:ABC-DEF...">
                            <div class="mt-2 flex items-center gap-2">
                                <button type="button" wire:click="fetchTelegramChatIds" class="text-xs font-semibold text-primary hover:underline">Fetch Chat ID</button>
                                <span class="text-[11px] text-gray-400">Automatically retrieve your Chat IDs to select where you want to receive notifications.</span>
                            </div>
                            @if($telegramFetchMessage)
                                <p class="mt-1 text-[11px] {{ str_contains($telegramFetchMessage, 'Webhook is active') ? 'text-red-600' : 'text-gray-500' }}">
                                    {{ $telegramFetchMessage }}
                                </p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600">Chat ID</label>
                            <input wire:model="settings.admin_telegram_chat_id" type="text" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm px-3 py-2" placeholder="-1001234567890">
                            @if(!empty($telegramChatOptions))
                                <select wire:model="settings.admin_telegram_chat_id" class="mt-2 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm px-3 py-2 bg-white">
                                    <option value="">Select from recent chats</option>
                                    @foreach($telegramChatOptions as $chat)
                                        <option value="{{ $chat['id'] }}">{{ $chat['label'] }} ({{ $chat['id'] }})</option>
                                    @endforeach
                                </select>
                            @endif
                            @error('settings.admin_telegram_chat_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 flex items-center justify-end gap-3">
                @if($savedSection === 'notifications')
                    <span class="text-xs font-semibold text-green-600">Saved</span>
                @endif
                <button
                    wire:click="saveAdminNotifications"
                    wire:loading.attr="disabled"
                    class="bg-primary hover:bg-primary text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-sm transition"
                >
                    Save Notifications
                </button>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 p-5 bg-white">
            <h4 class="text-base font-bold text-gray-800">Live Chat</h4>
            <p class="text-xs text-gray-500 mt-1 mb-4">Enable customer live chat on the storefront.</p>

            <div class="flex items-start gap-3">
                <input type="checkbox" wire:model="settings.live_chat_enabled" class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary mt-1">
                <div>
                    <p class="text-sm font-semibold text-gray-800">Enable Live Chat Widget</p>
                    <p class="text-xs text-gray-500">Adds a storefront chat widget so customers can message you instantly. Set Telegram Config first, then enable this to forward messages and deliver replies.</p>
                    <ol class="mt-2 text-[11px] text-gray-500 list-decimal list-inside space-y-1">
                        <li>Open Telegram and search for <span class="font-semibold">@BotFather</span>.</li>
                        <li>Create a bot by sending <span class="font-semibold">/newbot</span>.</li>
                        <li>Follow the prompts to set a name and username (username must end with <span class="font-semibold">bot</span>).</li>
                        <li>Save the Bot Token (looks like 123456789:ABCDefGhIJKlmNoPQRstuv).</li>
                        <li>Create a Telegram group and add your bot.</li>
                        <li>Turn on Topics in the group settings and make the bot an admin with permission to manage topics.</li>
                        <li>Paste Bot Token above, set Chat ID (you can fetch it), save settings, then set the webhook below.</li>
                    </ol>
                    @error('settings.live_chat_enabled')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Webhook</p>
                        <p class="text-xs text-gray-500">Required for Telegram to deliver your admin replies back to the customer on your site.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="setTelegramWebhook" class="text-xs font-semibold text-primary hover:underline">Set Webhook</button>
                        @if($telegramWebhookMessage)
                            <span class="text-[11px] text-gray-500">{{ $telegramWebhookMessage }}</span>
                        @endif
                        @if($telegramWebhookSet || (!empty($settings['admin_telegram_webhook_set']) && $settings['admin_telegram_webhook_set']))
                            <span class="text-[11px] font-semibold text-green-600">[Saved]</span>
                        @endif
                        <button type="button" wire:click="clearTelegramWebhook" class="text-xs font-semibold text-gray-500 hover:text-gray-700">Clear</button>
                    </div>
                </div>
                <p class="mt-2 text-[11px] text-gray-500">Tip: Clear webhook before fetching Chat ID.</p>
            </div>

            <div class="mt-5 flex items-center justify-end gap-3">
                @if($savedSection === 'notifications')
                    <span class="text-xs font-semibold text-green-600">Saved</span>
                @endif
                <button
                    wire:click="saveAdminNotifications"
                    wire:loading.attr="disabled"
                    class="bg-primary hover:bg-primary text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-sm transition"
                >
                    Save Live Chat
                </button>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 p-5 bg-white">
            <h4 class="text-base font-bold text-gray-800">Cron & Queue Setup</h4>
            <p class="text-xs text-gray-500 mt-1 mb-4">Use cron to process queued jobs on shared hosting and VPS.</p>

            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Cron Command</label>
                    <div class="mt-1 flex items-center gap-2">
                        <input type="text" readonly value="* * * * * php {{ base_path('artisan') }} schedule:run >> /dev/null 2>&1" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-xs bg-gray-50">
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1">Set this in your hosting cron manager (runs every minute).</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Queue Driver</label>
                    <input type="text" readonly value="QUEUE_CONNECTION=database" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-xs bg-gray-50">
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" wire:click="sendQueueTest" class="bg-primary text-white text-xs font-semibold rounded-lg px-3 py-2 hover:bg-primary">Send Test Job</button>
                    @if($queueTestMessage)
                        <span class="text-[11px] text-gray-500">{{ $queueTestMessage }}</span>
                    @endif
                </div>
                <div class="text-[11px] text-gray-500">
                    Last processed at: {{ \App\Models\Setting::where('key','queue_last_processed_at')->value('value') ?? 'Not yet processed' }}
                </div>
            </div>
        </section>
    </div>
</div>
